<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user(); // Get authenticated user
        $role = $user->role;

        // Common validation
        $rules = [
            'date_reported' => 'required|date',
            'subject' => 'required|string',
        ];

        // Extra fields only required for students
        if ($role === 'student') {
            $rules['student_no'] = 'required|digits:9';
            $rules['yearlvl_degree'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Generate unique ticket number
        do {
            $ticketNo = 'CMP-' . rand(1000, 9999);
        } while (Complaint::where('ticket_no', $ticketNo)->exists());

        // Create complaint
        $complaint = Complaint::create([
            'user_id' => $user->id,
            'ticket_no' => $ticketNo,
            'reporter_name' => $user->first_name . ' ' . $user->last_name,
            'student_no' => $validated['student_no'] ?? null,
            'date_reported' => $validated['date_reported'],
            'yearlvl_degree' => $validated['yearlvl_degree'] ?? null,
            'subject' => $validated['subject'],
            'status' => 'Pending',
            'meeting_schedule' => null,
        ]);

        return response()->json([
            'message' => 'Complaint submitted successfully!',
            'data' => $complaint
        ], 201);
    }


    public function myComplaints(Request $request)
    {
        $user = $request->user();

        $complaints = Complaint::where('user_id', $user->id)
            ->orderBy('date_reported', 'desc')
            ->get();

        return response()->json($complaints);
    }
}
