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

        $violations = $query->latest()->paginate(10);

        // Distinct filter options (for dropdowns)
        $offenses = Violation::select('offense')->distinct()->pluck('offense');
        $statuses = ['Pending', 'Complete'];

      $stats = Violation::selectRaw("
    student_no,
    MAX(full_name) AS full_name,
    MAX(student_email) AS student_email,
    MAX(yearlvl_degree) AS yearlvl_degree,
    SUM(CASE WHEN level = 'Minor' THEN 1 ELSE 0 END) AS total_minors,
    SUM(CASE WHEN level = 'Major' THEN 1 ELSE 0 END) AS total_majors,
    SUM(CASE WHEN level = 'Major' AND status = 'Pending' THEN 1 ELSE 0 END) AS pending_majors,
    MAX(CASE WHEN level = 'Major' THEN action_taken END) AS last_action,  
    MAX(CASE WHEN level = 'Major' THEN date_reported END) AS last_major_date
")
->groupBy('student_no')
->get()
->map(function ($s) {
    $s->minors_after_last_major = Violation::where('student_no', $s->student_no)
        ->where('level', 'Minor')
        ->where('date_reported', '>', ($s->last_major_date ?? '0000-00-00'))
        ->count();

    $s->escalated = $s->minors_after_last_major >= 3;
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
        ]);

        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', 'Violation added successfully.');
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
        return back()->with('success', 'Violation marked as resolved.');
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

        return redirect()->route($this->getRoutePrefix() . 'violations.index')
            ->with('success', "Escalation resolved with ticket {$violationNo}.");
    }




    protected function getRoutePrefix()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin' ? 'admin.' : '';
    }
}
