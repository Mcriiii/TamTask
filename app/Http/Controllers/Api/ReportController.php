<?php

namespace App\Http\Controllers\Api;

use App\Models\Incident;
use App\Models\Complaint;
use App\Models\LostFound;
use App\Models\Violation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function getReportCounts(Request $request)
    {
        $user = $request->user();

        $violationCount = Violation::where('user_id', $user->id)->count();
        $complaintCount = Complaint::where('student_no', $user->student_no)->count(); // assuming student_no is on users table
        $lostFoundCount = LostFound::where('email', $user->email)->count();
        $incidentCount = Incident::where('reporter_name', $user->first_name . ' ' . $user->last_name)->count();

        return response()->json([
            'complaint_count' => $complaintCount,
            'lost_found_count' => $lostFoundCount,
            'violation_count' => $violationCount,
            'incident_count' => $incidentCount,
        ]);
    }
}
