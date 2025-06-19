<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
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

        do {
            $ticketNo = 'CERT-' . rand(1000, 9999);
        } while (Certificate::where('ticket_no', $ticketNo)->exists());

        // Check if student has pending major violations
        $hasPendingMajor = Violation::where('student_no', $request->student_no)
            ->where('level', 'Major')
            ->where('status', 'Pending')
            ->exists();

        $status = $hasPendingMajor ? 'Declined' : 'Pending';

        Certificate::create([
            'ticket_no' => $ticketNo,
            'requester_name' => $request->requester_name,
            'student_no' => $request->student_no,
            'email' => $request->email,
            'yearlvl_degree' => $request->yearlvl_degree,
            'date_requested' => $request->date_requested,
            'purpose' => $request->purpose,
            'status' => $status,
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
            'status' => 'required|in:Pending,Accepted,Declined',
        ]);

        $certificate = Certificate::findOrFail($id);
        $certificate->update($request->only([
            'requester_name',
            'student_no',
            'email',
            'yearlvl_degree',
            'date_requested',
            'purpose',
            'status',
        ]));

        // But also check if status needs to be corrected due to major violation
        $hasPendingMajor = \App\Models\Violation::where('student_no', $request->student_no)
            ->where('level', 'Major')
            ->where('status', 'Pending')
            ->exists();

        if ($hasPendingMajor && $certificate->status !== 'Declined') {
            $certificate->status = 'Declined';
            $certificate->save();
        } elseif (!$hasPendingMajor && $certificate->status === 'Declined') {
            $certificate->status = 'Pending';
            $certificate->save();
        }

        return redirect()->back()->with('success', 'Certificate updated successfully.');
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return redirect()->back()->with('success', 'Certificate deleted successfully.');
    }

    public function exportPdf($id)
    {
        $certificate = Certificate::findOrFail($id);

        $pdf = Pdf::loadView('admin.pdf.single_certificate', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('certificate_' . $certificate->ticket_no . '.pdf');
    }
}
