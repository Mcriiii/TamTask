<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Referral;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = Referral::query();

        if ($request->filled('date')) {
            $query->whereDate('date_reported', '=', date('Y-m-d', strtotime($request->date)));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('referral_no', 'like', '%' . $request->search . '%')
                  ->orWhere('student_name', 'like', '%' . $request->search . '%');
            });
        }

        $referrals = $query->latest()->paginate(10);

        return view('referrals', compact('referrals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'referral_no'   => 'required|unique:referrals,referral_no',
            'date_reported' => 'required|date',
            'level'         => 'required',
            'student_name'  => 'required|string|max:255',
            'date_to_see'   => 'required|date',
            'status'        => 'required|in:Pending,Complete',
        ]);

        Referral::create($request->all());

        return redirect()->route('referrals.index')
                         ->with('success', 'Referral added.');
    }

    public function edit($id)
    {
        $referral = Referral::findOrFail($id);

        return response()->json([
            'html' => view('partials.referral_edit_form', compact('referral'))->render()
        ]);
    }

    public function update(Request $request, $id)
    {
        $referral = Referral::findOrFail($id);

        $request->validate([
            'date_reported' => 'required|date',
            'level'         => 'required',
            'student_name'  => 'required|string|max:255',
            'date_to_see'   => 'required|date',
            'status'        => 'required|in:Pending,Complete',
        ]);

        $referral->update($request->only([
            'date_reported',
            'level',
            'student_name',
            'date_to_see',
            'status',
        ]));

        return redirect()->route('referrals.index')
                         ->with('success', 'Referral updated.');
    }

    public function destroy($id)
    {
        $referral = Referral::findOrFail($id);
        $referral->delete();

        return redirect()->route('referrals.index')
                         ->with('success', 'Referral deleted.');
    }
}
