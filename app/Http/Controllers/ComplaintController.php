<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    // Display the list of complaints with filters
    public function index(Request $request)
    {
        $query = Complaint::query();

        // Search by student_no, ticket_no, yearlvl_degree, or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_no', 'like', "%{$search}%")
                  ->orWhere('ticket_no', 'like', "%{$search}%")
                  ->orWhere('yearlvl_degree', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Filter by subject dropdown
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        $complaints = $query->latest()->paginate(10);
        $ticketNo = 'CMP-' . strtoupper(uniqid());

        return view('complaints', compact('complaints', 'ticketNo'));
    }

    // Store a new complaint
    public function store(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|unique:complaints,ticket_no',
            'reporter_name' => 'required|string|max:255',
            'student_no' => 'required|string|max:50',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
        ]);

        Complaint::create($request->all());

        return redirect()->route('complaints.index')->with('success', 'Complaint submitted successfully!');
    }

    // Return edit form for modal (AJAX)
    public function edit($id)
    {
        $complaint = Complaint::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('partials.complaint_edit_form', compact('complaint'))->render()
            ]);
        }

        return abort(404);
    }

    // Update a complaint
    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'reporter_name' => 'required|string|max:255',
            'student_no' => 'required|string|max:50',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
        ]);

        $complaint->update($request->all());

        return redirect()->route('complaints.index')->with('success', 'Complaint updated successfully.');
    }

    // Delete a complaint
    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return redirect()->route('complaints.index')->with('success', 'Complaint deleted successfully.');
    }
}
