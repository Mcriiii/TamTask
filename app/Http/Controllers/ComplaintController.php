<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    // Display the list of complaints with filters
    public function index(Request $request)
    {

        Complaint::whereNotNull('meeting_schedule')
            ->where('status', 'Ongoing')
            ->where('meeting_schedule', '<', now())
            ->update(['status' => 'Resolved']);

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('yearlvl_degree')) {
            $query->where('yearlvl_degree', $request->yearlvl_degree);
        }

        $complaints = $query->latest()->paginate(10);

        $view = $this->getRoutePrefix() === 'admin.' ? 'admin.complaints' : 'complaints';

        $subjects = Complaint::select('subject')->distinct()->pluck('subject')->sort();

        return view($view, compact('complaints', 'subjects'));
    }

    // Store a new complaint
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_name' => ['required', 'regex:/^[\pL\s]+$/u'],
            'date_reported' => 'required|date',
            'subject' => 'required|string|max:1000',
            'complaint_role' => 'required|in:student,teacher,security',
        ]);

        // Only require student_no and yearlvl_degree if role is student
        $validator->sometimes('student_no', ['required', 'regex:/^\d{9}$/'], function ($input) {
            return $input->complaint_role === 'student';
        });

        $validator->sometimes('yearlvl_degree', ['required', 'string', 'max:100'], function ($input) {
            return $input->complaint_role === 'student';
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all() + ['_modal' => 'add']);
        }
        do {
            $ticketNo = 'CMP-' . rand(1000, 9999);
        } while (Complaint::where('ticket_no', $ticketNo)->exists());

        Complaint::create([
            'user_id' => Auth::id(),
            'ticket_no' => $ticketNo,
            'reporter_name' => $request->reporter_name,
            'student_no' => $request->complaint_role === 'student' ? $request->student_no : null,
            'date_reported' => $request->date_reported,
            'yearlvl_degree' => $request->complaint_role === 'student' ? $request->yearlvl_degree : null,
            'subject' => $request->subject,
            'status' => 'Pending',
        ]);
        return redirect()->route($this->getRoutePrefix() . 'complaints.index')
            ->with('success', "Complaint submitted! Ticket No: {$ticketNo}");
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

        $validator = Validator::make($request->all(), [
            'reporter_name' => ['required', 'string', 'max:255'],
            'date_reported' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'meeting_schedule' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
            'complaint_role' => ['required', 'in:student,teacher,security'],
        ]);

        $validator->sometimes('student_no', ['required', 'regex:/^\d{9}$/'], function ($input) {
            return $input->complaint_role === 'student';
        });

        $validator->sometimes('yearlvl_degree', ['required', 'string', 'max:100'], function ($input) {
            return $input->complaint_role === 'student';
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all() + [
                    '_modal' => 'edit',
                    'id' => $id // ✅ Include the complaint ID so the Blade can regenerate the correct action URL
                ])
                ->withErrors($validator);
        }

        // Manually update fields
        $complaint->reporter_name = $request->reporter_name;
        $complaint->student_no = $request->complaint_role === 'student' ? $request->student_no : null;
        $complaint->date_reported = $request->date_reported;
        $complaint->yearlvl_degree = $request->complaint_role === 'student' ? $request->yearlvl_degree : null;
        $complaint->subject = $request->subject;

        // Handle meeting_schedule
        if ($request->filled('meeting_schedule')) {
            $complaint->meeting_schedule = $request->meeting_schedule;

            // Automatically update status if still Pending
            if ($complaint->status === 'Pending') {
                $complaint->status = 'Ongoing';
            }
        }

        // Allow manual override from form if status was submitted
        if ($request->has('status')) {
            $complaint->status = $request->status;
        }

        $complaint->save();

        return redirect()->route($this->getRoutePrefix() . 'complaints.index')
            ->with('success', 'Complaint updated successfully.');
    }

    // Delete a complaint
    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return redirect()->route($this->getRoutePrefix() . 'complaints.index')
            ->with('success', 'Complaint deleted successfully.');
    }

    protected function getRoutePrefix()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin' ? 'admin.' : '';
    }
}
