<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Violation;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $query = Violation::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('violation_no', 'like', '%' . $request->search . '%')
                  ->orWhere('offense', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_reported')) {
            $query->whereDate('date_reported', $request->date_reported);
        }

        $violations = $query->latest()->paginate(10);
        return view('violation', compact('violations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'student_no' => 'required|string|max:50',
            'student_email' => 'required|email|max:255',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string|max:255',
            'offense' => 'required|string',
            'status' => 'required|in:Pending,Complete',
        ]);

        $violationNo = 'VIO-' . rand(1000, 9999);

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

        return redirect()->route('violations.index')->with('success', 'Violation report submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $violation = Violation::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'student_no' => 'required|string|max:50',
            'student_email' => 'required|email|max:255',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required|string|max:255',
            'offense' => 'required|string',
            'status' => 'required|in:Pending,Complete',
        ]);

        $violation->update([
            'full_name' => $request->full_name,
            'student_no' => $request->student_no,
            'student_email' => $request->student_email,
            'date_reported' => $request->date_reported,
            'yearlvl_degree' => $request->yearlvl_degree,
            'offense' => $request->offense,
            'level' => $this->determineLevel($request->offense),
            'status' => $request->status,
        ]);

        return redirect()->route('violations.index')->with('success', 'Violation updated successfully.');
    }

    public function destroy($id)
    {
        $violation = Violation::findOrFail($id);
        $violation->delete();

        return redirect()->route('violations.index')->with('success', 'Violation deleted successfully.');
    }

    private function determineLevel($offense)
    {
        $minor = [
            'Not wearing prescribed uniform/attire',
            'Entry without ID',
            'Possession of pornographic materials in any form/medium',
            'Possession of any harmful gadget/weapon',
            'Possession of cigarette and e-cigarette on campus',
            'Possession of alcoholic drink',
            'Simple misconduct',
            'Smoking on campus',
            'Eating and drinking in restricted areas',
            'Public display of intimacy',
            'All other acts embodied in the classroom policy including those of full online classes policies',
            'All other acts/offenses of misconduct in any form',
        ];

        return in_array($offense, $minor) ? 'Minor' : 'Major';
    }
}
