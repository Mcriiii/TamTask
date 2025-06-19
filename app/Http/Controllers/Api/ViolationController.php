<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Violation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ViolationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'student_no' => ['required', 'regex:/^\d{9}$/'],
            'student_email' => 'required|email',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string',
            'offense' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate violation number
        do {
            $violationNo = 'VIO-' . rand(1000, 9999);
        } while (Violation::where('violation_no', $violationNo)->exists());

        $user = Auth::user();

        $violation = Violation::create([
            'violation_no'    => $violationNo,
            'full_name'       => $request->full_name,
            'student_no'      => $request->student_no,
            'student_email'   => $request->student_email,
            'date_reported'   => $request->date_reported,
            'yearlvl_degree'  => $request->yearlvl_degree,
            'offense'         => $request->offense,
            'level'           => $this->determineLevel($request->offense),
            'status'          => 'Pending',
            'user_id'         => $user ? $user->id : null,
        ]);

        // Auto-escalate if needed
        $this->checkAndEscalateMinors($request->student_no);

        return response()->json(['message' => 'Violation submitted successfully', 'data' => $violation], 201);
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

    private function checkAndEscalateMinors($student_no)
    {
        $nextBatch = Violation::where('student_no', $student_no)
            ->where('level', 'Minor')
            ->where('escalation_resolved', false)
            ->orderBy('date_reported', 'asc')
            ->take(3)
            ->get();
        // ✅ Generate a unique violation number
        do {
            $violationNo = 'VIO-' . rand(1000, 9999);
        } while (Violation::where('violation_no', $violationNo)->exists());

        if ($nextBatch->count() === 3) {
            Violation::create([
                'violation_no'    => $violationNo,
                'student_no'      => $student_no,
                'full_name'       => $nextBatch[0]->full_name,
                'student_email'   => $nextBatch[0]->student_email,
                'yearlvl_degree'  => $nextBatch[0]->yearlvl_degree,
                'date_reported'   => now()->toDateString(),
                'offense'         => 'Escalation: reached 3 minors',
                'level'           => 'Major',
                'status'          => 'Pending',
                'user_id'         => Auth::id(),
            ]);

            foreach ($nextBatch as $minor) {
                $minor->update(['escalation_resolved' => true]);
            }

            \App\Models\Certificate::where('student_no', $student_no)
                ->where('status', 'Pending')
                ->update(['status' => 'Declined']);
        }
    }
}
