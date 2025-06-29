<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;  // Add this line
use App\Http\Controllers\Controller;

class IncidentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'incident' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'max:1000'],  // Incident description
            'reporter_name' => ['required', 'regex:/^[\pL\s]+$/u'], // Reporter name
            'date_reported' => 'required|date',  // Date reported
        ]);

        // Generate a unique ticket number (format: INC-xxxx)
        do {
            $ticketNo = 'INC-' . rand(1000, 9999);  // Ticket number format
        } while (Incident::where('ticket_no', $ticketNo)->exists());  // Ensure the ticket number is unique

        // Add the reporter role (retrieved from the authenticated user)
        $reporterRole = Auth::user() ? Auth::user()->role : 'user';  // Default to 'user' if not authenticated

        // Create the incident record in the database
        $incident = Incident::create([
            'ticket_no' => $ticketNo,  // Assign the generated ticket number
            'incident' => $validated['incident'],  // Incident description
            'reporter_name' => $validated['reporter_name'], // Reporter name
            'date_reported' => $validated['date_reported'],  // Date reported
            'level' => (new Incident($validated))->level,  // Auto-compute the incident level based on description
            'status' => 'Pending',  // Default status to 'Pending' for new incidents
            'reporter_role' => $reporterRole,  // Add the reporter_role (can be from Auth or default)
            'user_id' => Auth::id(),
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Incident reported successfully!',
            'data' => $incident
        ], 201);  // 201 Created status
    }

    public function myIncidents(Request $request)
    {
        $user = $request->user();
        $incidents = Incident::where('user_id', $user->id)->orderBy('date_reported', 'desc')->get();
        return response()->json($incidents);
    }
}
