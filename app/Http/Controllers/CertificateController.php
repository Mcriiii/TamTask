<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::query();

        if ($request->filled('search')) {
            $query->where('requester_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        $certificates = $query->latest()->paginate(10);
        $view = Auth::user()->role === 'admin' ? 'admin.certificate' : 'certificate';

        return view($view, compact('certificates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'student_no' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'yearlvl_degree' => 'required|string|max:255',
            'date_requested' => 'required|date',
            'purpose' => 'required|in:Transfer,Scholarship,Internship',
        ]);

        $ticketNo = 'CERT-' . rand(1000, 9999);

        Certificate::create([
            'ticket_no' => $ticketNo,
            'requester_name' => $request->requester_name,
            'student_no' => $request->student_no,
            'email' => $request->email,
            'yearlvl_degree' => $request->yearlvl_degree,
            'date_requested' => $request->date_requested,
            'purpose' => $request->purpose,
        ]);

        return redirect()->back()->with('success', 'Certificate request added.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'student_no' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'yearlvl_degree' => 'required|string|max:255',
            'date_requested' => 'required|date',
            'purpose' => 'required|in:Transfer,Scholarship,Internship',
        ]);

        $certificate = Certificate::findOrFail($id);
        $certificate->update($request->only([
            'requester_name',
            'student_no',
            'email',
            'yearlvl_degree',
            'date_requested',
            'purpose',
        ]));

        return redirect()->back()->with('success', 'Certificate updated successfully.');
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return redirect()->back()->with('success', 'Certificate deleted successfully.');
    }
}
