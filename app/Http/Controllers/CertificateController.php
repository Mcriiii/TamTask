<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class CertificateController extends Controller
{

    protected function getRoutePrefix()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin' ? 'admin.' : '';
    }


    public function index(Request $request)
    {
        $query = Certificate::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('requester_name', 'like', "%{$search}%")
                    ->orWhere('student_no', 'like', "%{$search}%")
                    ->orWhere('ticket_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        $certificates = $query->latest()->paginate(10);
        $view = $this->getRoutePrefix() . 'certificate';

        return view($view, compact('certificates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requester_name' => 'required|string|max:255',
            'student_no' => ['required', 'regex:/^\d{9}$/'],
            'email' => 'email|max:255',
            'yearlvl_degree' => 'required|string|max:255',
            'date_requested' => 'required|date',
            'purpose' => 'required|in:Transfer,Scholarship,Internship',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('_modal', 'add'); // ✅ This triggers the add modal to reopen
        }

        do {
            $ticketNo = 'CERT-' . rand(1000, 9999);
        } while (Certificate::where('ticket_no', $ticketNo)->exists());

        $hasPendingMajor = Violation::where('student_no', $request->student_no)
            ->where('level', 'Major')
            ->where('status', 'Pending')
            ->exists();

        Certificate::create([
            'ticket_no' => $ticketNo,
            'requester_name' => $request->requester_name,
            'student_no' => $request->student_no,
            'email' => $request->email,
            'yearlvl_degree' => $request->yearlvl_degree,
            'date_requested' => $request->date_requested,
            'purpose' => $request->purpose,
            'status' => $hasPendingMajor ? 'Declined' : 'Pending',
        ]);

        return redirect()->route($this->getRoutePrefix() . 'certificates.index')
            ->with('success', 'Certificate request added.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'requester_name' => 'required|string|max:255',
            'student_no' => ['required', 'regex:/^\d{9}$/'],
            'email' => 'email|max:255',
            'yearlvl_degree' => 'required|string|max:255',
            'date_requested' => 'required|date',
            'purpose' => 'required|in:Transfer,Scholarship,Internship',
            'status' => 'required|in:Pending,Accepted,Declined',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('_modal', 'edit')
                ->with('edit_id', $id);
        }

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
        $hasPendingMajor = Violation::where('student_no', $request->student_no)
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

        return redirect()->route($this->getRoutePrefix() . 'certificates.index')
            ->with('success', 'Certificate updated successfully.');
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return redirect()->route($this->getRoutePrefix() . 'certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    public function exportPdf($id)
    {
        $certificate = Certificate::findOrFail($id);

        $pdf = Pdf::loadView('admin.pdf.single_certificate', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('certificate_' . $certificate->ticket_no . '.pdf');
    }

    public function uploadReceipt(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('_receipt_error', $id); // To show the modal again
        }

        try {
            $certificate = Certificate::findOrFail($id);

            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('receipts', $filename, 'public');

                $certificate->receipt_path = 'receipts/' . $filename;
                $certificate->status = 'Uploaded';
                $certificate->save();
            }

            return redirect()->route($this->getRoutePrefix() . 'certificates.index')
                ->with('success', 'Receipt uploaded and status updated to Uploaded.');
        } catch (Exception $e) {
            return back()->with('error', 'Upload failed. Please try again.');
        }

    }

    public function updateFileStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Uploaded,Ready for Release,Released',
        ]);

        $certificate = Certificate::findOrFail($id);
        $certificate->status = $request->status;
        $certificate->save();

        return redirect()->route($this->getRoutePrefix() . 'certificates.index')
            ->with('success', 'Status updated successfully.');
    }
}
