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
            'violation_no' => 'required|unique:violations,violation_no',
            'full_name' => 'required',
            'student_no' => 'required',
            'student_email' => 'required|email',
            'date_reported' => 'required|date',
            'yearlvl_degree' => 'required',
            'offense' => 'required',
            'status' => 'required|in:Pending,Complete',
        ]);

        $data = $request->all();
        $data['level'] = $this->determineLevel($data['offense']);
        Violation::create($data);

        return redirect()->route('admin.violations.index')->with('success', 'Violation added.');
    }

    public function destroy($id)
    {
        Violation::findOrFail($id)->delete();
        return redirect()->route('admin.violations.index')->with('success', 'Violation deleted.');
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
