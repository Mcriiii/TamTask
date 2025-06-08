<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $query = Violation::query();

        if ($request->search) {
            $query->where('full_name', 'like', '%' . $request->search . '%')
                ->orWhere('student_no', 'like', '%' . $request->search . '%')
                ->orWhere('student_email', 'like', '%' . $request->search . '%');
        }

        if ($request->date_reported) {
            $query->whereDate('date_reported', $request->date_reported);
        }

        $violations = $query->orderBy('date_reported', 'desc')->paginate(10);

        return view('violation', compact('violations'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'full_name' => 'required',
        'student_no' => 'required',
        'student_email' => 'required|email',
        'date_reported' => 'required|date',
        'yearlvl_degree' => 'required',
        'offense' => 'required',
        'level' => 'required|in:Minor,Major',
        'status' => 'required|in:Pending,Complete',
        'action_taken' => 'nullable|in:Warning,DUSAP,Suspension,Expulsion'
    ]);

    // Auto-generate Violation Number
    $validated['violation_no'] = 'VIO-' . strtoupper(uniqid());

    Violation::create($validated);

    return redirect()->route('violations.index')->with('success', 'Violation Report Added Successfully.');
}


    public function update(Request $request, $id)
    {
        $violation = Violation::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required',
            'student_no' => 'required',
            'student_email' => 'required|email',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required',
            'offense' => 'required',
            'level' => 'required|in:Minor,Major',
            'status' => 'required|in:Pending,Complete',
            'action_taken' => 'nullable|in:Warning,DUSAP,Suspension,Expulsion'
        ]);

        $violation->update($validated);

        return redirect()->back()->with('success', 'Violation Report Updated Successfully.');
    }

    public function destroy($id)
    {
        $violation = Violation::findOrFail($id);
        $violation->delete();

        return redirect()->back()->with('success', 'Violation Report Deleted Successfully.');
    }
}
