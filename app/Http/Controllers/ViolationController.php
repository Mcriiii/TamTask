<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Violation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $query = Violation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('violation_no', 'like', "%{$search}%")
                    ->orWhere('offense', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_reported')) {
            $query->whereDate('date_reported', $request->date_reported);
        }

        $violations = $query->with('user')->latest()->paginate(10);

        // Distinct filter options (for dropdowns)
        $offenses = Violation::select('offense')->distinct()->pluck('offense');
        $statuses = ['Pending', 'Complete'];

        $stats = Violation::selectRaw("
            student_no,
            MAX(full_name) AS full_name,
            MAX(student_email) AS student_email,
            MAX(yearlvl_degree) AS yearlvl_degree,
            COUNT(*) AS total_violations,
            SUM(CASE WHEN level = 'Minor' THEN 1 ELSE 0 END) AS total_minors,
            SUM(CASE WHEN level = 'Major' THEN 1 ELSE 0 END) AS total_majors
        ")
            ->groupBy('student_no')
            ->get()
            ->map(function ($s) {
                // Pending and resolved minors
                $s->pending_minors = Violation::where('student_no', $s->student_no)
                    ->where('level', 'Minor')
                    ->where('status', 'Pending')
                    ->count();

                $s->resolved_minors = $s->total_minors - $s->pending_minors;

                // Pending and resolved majors
                $s->pending_majors = Violation::where('student_no', $s->student_no)
                    ->where('level', 'Major')
                    ->where('status', 'Pending')
                    ->count();

                $s->resolved_majors = $s->total_majors - $s->pending_majors;

                // Escalation check logic
                $pendingMinorsList = Violation::where('student_no', $s->student_no)
                    ->where('level', 'Minor')
                    ->where('status', 'Pending')
                    ->orderBy('date_reported', 'asc')
                    ->get();

                $escalated = false;
                if ($pendingMinorsList->count() >= 3) {
                    $earliest3 = $pendingMinorsList->take(3);
                    $firstDate = $earliest3->first()->date_reported ?? null;

                    $alreadyEscalated = Violation::where('student_no', $s->student_no)
                        ->where('level', 'Major')
                        ->where('offense', 'Escalation: reached 3 minors')
                        ->whereDate('created_at', '>=', $firstDate)
                        ->exists();

                    $escalated = !$alreadyEscalated;
                }

                $s->escalated = $escalated;
                return $s;
            });

        $view = Auth::user()->role === 'admin' ? 'admin.violation' : 'violation';
        return view($view, compact('violations', 'offenses', 'statuses', 'stats'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'student_no' => ['required', 'regex:/^\d{9}$/'],
            'student_email' => 'required|email',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string',
            'offense' => 'required|string',
            'status' => 'required|in:Pending,Complete',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('_modal', 'add');
        }

        // 🔐 Generate unique violation number: VIO-XXXX
        do {
            $violationNo = 'VIO-' . rand(1000, 9999);
        } while (Violation::where('violation_no', $violationNo)->exists());

        Violation::create([
            'violation_no' => $violationNo,
            'full_name' => $request->full_name,
            'student_no' => $request->student_no,
            'student_email' => $request->student_email,
            'date_reported' => $request->date_reported,
            'yearlvl_degree' => $request->yearlvl_degree,
            'offense' => $request->offense,
            'level' => $this->determineLevel($request->offense),
            'status' => $request->status,
            'user_id' => Auth::id(), // ✅ track who reported
        ]);

        // ✅ Check for auto-escalation
        $this->checkAndEscalateMinors($request->student_no);

        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', 'Violation added successfully.');
    }

    private function checkAndEscalateMinors($student_no)
    {
        // Get the next batch of 3 minor violations not yet escalated
        $nextBatch = Violation::where('student_no', $student_no)
            ->where('level', 'Minor')
            ->where('escalation_resolved', false) // <== IMPORTANT: match your column name
            ->orderBy('date_reported', 'asc')
            ->take(3)
            ->get();

        if ($nextBatch->count() === 3) {
            Violation::create([
                'violation_no'   => 'VIO-' . rand(1000, 9999),
                'student_no'     => $student_no,
                'full_name'      => $nextBatch[0]->full_name,
                'student_email'  => $nextBatch[0]->student_email,
                'yearlvl_degree' => $nextBatch[0]->yearlvl_degree,
                'date_reported'  => now()->toDateString(),
                'offense'        => 'Escalation: reached 3 minors',
                'level'          => 'Major',
                'status'         => 'Pending',
                'user_id'        => Auth::id(),
            ]);

            // Mark these 3 as escalated
            foreach ($nextBatch as $minor) {
                $minor->update(['escalation_resolved' => true]); // again: use the name you chose
            }
            \App\Models\Certificate::where('student_no', $student_no)
            ->where('status', 'Pending')
            ->update(['status' => 'Declined']);
        }

    }


    public function update(Request $request, $id)
    {
        $violation = Violation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'student_no' => ['required', 'regex:/^\d{9}$/'],
            'student_email' => 'required|email',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string',
            'offense' => 'required|string',
            'status' => 'required|in:Pending,Complete',
            'action_taken' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('_modal', 'edit')
                ->with('edit_id', $id);
        }

        $violation->update([
            'full_name' => $request->full_name,
            'student_no' => $request->student_no,
            'student_email' => $request->student_email,
            'date_reported' => $request->date_reported,
            'yearlvl_degree' => $request->yearlvl_degree,
            'offense' => $request->offense,
            'level' => $this->determineLevel($request->offense),
            'status' => $request->status,
            'action_taken' => $request->action_taken,
        ]);

        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', 'Violation updated successfully.');
    }

    public function destroy($id)
    {
        Violation::destroy($id);
        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', 'Violation deleted successfully.');
    }

    private function determineLevel($offense)
    {
        $major = [
            'Possession of prohibited drug',
            'Possession of explosive materials',
            'Possession of deadly weapon/firearms',
            'Acts of subversion, rebellion and inciting to sedition',
            'Possession of offensive/subversive materials',
            'Distribution of offensive/subversive materials in person or via electronic medium',
            'Being under the influence of liquor/prohibited drugs'
        ];

        return in_array($offense, $major) ? 'Major' : 'Minor';
    }


    public function takeAction(Request $req, $id)
    {
        $req->validate(['action_taken' => 'required|string']);
        $violation = Violation::findOrFail($id);
        $violation->action_taken = $req->action_taken;
        $violation->status = 'Complete';
        $violation->save();

        // 🔁 Auto-update certificates if no pending majors
        $studentNo = $violation->student_no;
        $hasPendingMajor = Violation::where('student_no', $studentNo)
            ->where('level', 'Major')
            ->where('status', 'Pending')
            ->exists();

        if (!$hasPendingMajor) {
            \App\Models\Certificate::where('student_no', $studentNo)
                ->where('status', 'Declined')
                ->update(['status' => 'Pending']);
        }
        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', 'Violation marked as resolved.');
    }


    public function resolveStudent(Request $req, $student_no)
    {
        $req->validate([
            'action_taken'   => 'required|string',
            'full_name'      => 'required|string',
            'student_email'  => 'required|email',
            'yearlvl_degree' => 'required|string',
        ]);

        //  Mark pending minors as Complete
        Violation::where('student_no', $student_no)
            ->where('level', 'Minor')
            ->where('status', 'Pending')
            ->update(['status' => 'Complete']);

        //  Generate a unique ticket across both create and resolve
        do {
            $violationNo = 'VIO-' . rand(1000, 9999);
        } while (Violation::where('violation_no', $violationNo)->exists());

        //  Create the new major violation record
        Violation::create([
            'violation_no'    => $violationNo,
            'student_no'      => $student_no,
            'full_name'       => $req->full_name,
            'student_email'   => $req->student_email,
            'date_reported'   => now()->toDateString(),
            'yearlvl_degree'  => $req->yearlvl_degree,
            'offense'         => 'Escalation: reached 3 minors',
            'level'           => 'Major',
            'status'          => 'Complete',
            'action_taken'    => $req->action_taken,
        ]);

        // ✅ If all major violations resolved, update certificate status
        $hasPendingMajor = Violation::where('student_no', $student_no)
            ->where('level', 'Major')
            ->where('status', 'Pending')
            ->exists();

        if (!$hasPendingMajor) {
            \App\Models\Certificate::where('student_no', $student_no)
                ->where('status', 'Declined')
                ->update(['status' => 'Accepted']);
        }

        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', "Escalation resolved with ticket {$violationNo}.");
    }




    protected function getRoutePrefix()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin' ? 'admin.' : '';
    }
}
