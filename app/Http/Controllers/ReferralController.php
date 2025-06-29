<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $view = Auth::user()->role === 'admin' ? 'admin.referrals' : 'referrals';

        return view($view, compact('referrals'));
    }


    public function store(Request $request)
    {
        do {
            $referralNo = 'REF-' . rand(1000, 9999);
        } while (Referral::where('referral_no', $referralNo)->exists()); // Ensure uniqueness

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'date_reported' => 'required|date',
            'level' => 'required',
            'student_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z]+$/'],
            'date_to_see' => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator) // return errors
                ->withInput()             // keep input data
                ->with('_modal', 'add');  // Specify that this is the add modal
        }

        // Create the new referral with the generated unique referral number
        Referral::create([
            'user_id' => Auth::id(),
            'referral_no' => $referralNo,
            'date_reported' => $request->date_reported,
            'level' => $request->level,
            'student_name' => $request->student_name,
            'date_to_see' => $request->date_to_see,
            'status' => 'Pending',
        ]);

        return redirect()->route($this->getRoutePrefix() . 'referrals.index')
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

        $validator = Validator::make($request->all(), [
            'date_reported' => 'required|date',
            'level' => 'required',
            'student_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z]+$/'],
            'date_to_see' => 'required|date',
            'status' => 'required|in:Pending,Complete',
        ]);

        // If validation fails, return errors and specify that it's an edit modal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)  // Pass validation errors to the view
                ->withInput()              // Keep the input data so the user doesn't have to re-enter it
                ->with('_modal', 'edit');  // Specify that this is an edit modal
        }

        $referral->update($request->only([
            'date_reported',
            'level',
            'student_name',
            'date_to_see',
            'status',
        ]));

        return redirect()->route($this->getRoutePrefix() . 'referrals.index')
            ->with('success', 'Referral updated.');
    }

    public function destroy($id)
    {
        $referral = Referral::findOrFail($id);
        $referral->delete();

        return redirect()->route($this->getRoutePrefix() . 'referrals.index')
            ->with('success', 'Referral deleted.');
    }

    protected function getRoutePrefix()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin' ? 'admin.' : '';
    }
}
