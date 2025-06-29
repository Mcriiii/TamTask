<?php

namespace App\Http\Controllers\Api;

use App\Models\LostFound;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LostFoundController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reporter_name' => ['required', 'regex:/^[\pL\s]+$/u'],
            'report_type' => 'required|in:LOS,FND',
            'email' => 'required|email',
            'date_reported' => 'required|date',
            'item_type' => 'required|string',
            'description' => 'required|string',
            'location_found' => 'nullable|string',
        ]);

        // 👇 Generate unique ticket_no based on report_type (LOS or FND)
        $prefix = $request->report_type;
        do {
            $ticketNo = $prefix . '-' . rand(1000, 9999);
        } while (LostFound::where('ticket_no', $ticketNo)->exists());

        $validated['ticket_no'] = $ticketNo;
        $validated['status'] = 'Unclaimed';

        LostFound::create($validated);

        return response()->json([
            'message' => 'Report submitted successfully',
            'ticket_no' => $ticketNo // return it so Android can show or save
        ], 201);
    }

    public function markAsClaimed($id)
    {
        $report = LostFound::findOrFail($id);

        if ($report->status === 'Claimed') {
            return response()->json(['message' => 'Item already claimed'], 400);
        }

        $report->update(['status' => 'Claimed']);

        return response()->json(['message' => 'Item marked as claimed']);
    }

    public function myReports(Request $request)
{
    $email = $request->user()->email; // assuming authenticated user

    $reports = LostFound::where('email', $email)
        ->orderBy('date_reported', 'desc')
        ->get();

    return response()->json($reports);
}
}
