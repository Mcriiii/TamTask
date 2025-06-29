<?php

namespace App\Http\Controllers\Api;

use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requester_name' => ['required', 'regex:/^[\pL\s]+$/u'],
            'email' => 'required|email',
            'student_no' => 'required|digits:9',
            'yearlvl_degree' => 'required|string',
            'date_requested' => 'required|date',
            'purpose' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Generate unique ticket number
        do {
            $ticketNo = 'CERT-' . rand(1000, 9999);
        } while (Certificate::where('ticket_no', $ticketNo)->exists());

        // Save to DB
        $certificate = Certificate::create([
            'ticket_no' => $ticketNo,
            'requester_name' => $request->requester_name,
            'email' => $request->email,
            'student_no' => $request->student_no,
            'yearlvl_degree' => $request->yearlvl_degree,
            'date_requested' => $request->date_requested,
            'purpose' => $request->purpose,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Certificate request submitted.',
            'ticket_no' => $ticketNo
        ]);
    }


    public function uploadReceipt(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);

        // Optional: Only allow upload if status is Accepted
        if ($certificate->status !== 'Accepted') {
            return response()->json(['message' => 'Certificate is not eligible for upload'], 403);
        }

        // Validate file
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = 'receipt_' . time() . '.' . $file->getClientOriginalExtension();

            // Store to /storage/app/public/receipts
            $file->storeAs('receipts', $filename, 'public');

            // Save to DB with 'receipts/filename.ext' (so asset('storage/'.$receipt_path) works)
            $certificate->receipt_path = 'receipts/' . $filename;
            $certificate->status = 'Uploaded';
            $certificate->save();
        }

        return response()->json(['message' => 'Receipt uploaded successfully']);
    }


    public function myCertificates(Request $request)
    {
        $user = $request->user();
        $certs = Certificate::where('email', $user->email)->get();
        return response()->json($certs);
    }
}
