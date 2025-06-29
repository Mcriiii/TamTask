<?php

// app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Models\Incident;
use App\Models\Referral;
use App\Models\Complaint;
use App\Models\LostFound;
use App\Models\Violation;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getCounts(Request $request)
    {
        $user = Auth::user();

        $data = [
            'complaints' => Complaint::where('reporter_name', $user->first_name . ' ' . $user->last_name)->count(),
            'lost_found' => LostFound::where('email', $user->email)->count(),
            'incidents'  => Incident::where('user_id', $user->id)->count(),
        ];

        // ✅ Add role-based data
        if ($user->role === 'student') {
            $data['certificates'] = Certificate::where('email', $user->email)->count();
        } elseif ($user->role === 'sfu') {
            $data['referrals'] = Referral::where('user_id', $user->id)->count(); // ✅ Include this
        } else {
            $data['violations'] = Violation::where('user_id', $user->id)->count();
        }

        return response()->json($data);
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $role = $user->role;
        $studentNo = $user->student_no ?? null;
        $since = now()->subDays(30);
        $notifications = [];

        if ($role === 'student') {
            $certs = Certificate::where('student_no', $studentNo)->where('updated_at', '>=', $since)->get();
            foreach ($certs as $item) {
                $notifications[] = [
                    'type' => 'Certificate',
                    'ticket_no' => $item->ticket_no,
                    'message' => "Certificate {$item->ticket_no} status: {$item->status}."
                ];
            }

            $complaints = Complaint::where('email', $user->email)->where('updated_at', '>=', $since)->get();
            foreach ($complaints as $item) {
                $notifications[] = [
                    'type' => 'Complaint',
                    'ticket_no' => $item->ticket_no,
                    'message' => "Complaint {$item->ticket_no} updated to {$item->status}."
                ];
            }

            $lostFound = LostFound::where('reporter_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($lostFound as $item) {
                $notifications[] = [
                    'type' => 'LostFound',
                    'ticket_no' => $item->ticket_no,
                    'message' => "{$item->ticket_no} item marked as {$item->status}."
                ];
            }

            $incidents = Incident::where('reporter_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($incidents as $item) {
                $notifications[] = [
                    'type' => 'Incident',
                    'ticket_no' => $item->ticket_no,
                    'message' => "Incident {$item->ticket_no} updated to {$item->status}."
                ];
            }
        }

        if (in_array($role, ['teacher', 'security'])) {
            $violations = Violation::where('user_id', $user->id)->where('updated_at', '>=', $since)->get();
            foreach ($violations as $item) {
                $notifications[] = [
                    'type' => 'Violation',
                    'ticket_no' => $item->violation_no,
                    'message' => "Violation {$item->violation_no} updated to {$item->status}."
                ];
            }

            $complaints = Complaint::where('reporter_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($complaints as $item) {
                $notifications[] = [
                    'type' => 'Complaint',
                    'ticket_no' => $item->ticket_no,
                    'message' => "Complaint {$item->ticket_no} updated to {$item->status}."
                ];
            }

            $lostFound = LostFound::where('reporter_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($lostFound as $item) {
                $notifications[] = [
                    'type' => 'LostFound',
                    'ticket_no' => $item->ticket_no,
                    'message' => "{$item->ticket_no} item marked as {$item->status}."
                ];
            }

            $incidents = Incident::where('reporter_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($incidents as $item) {
                $notifications[] = [
                    'type' => 'Incident',
                    'ticket_no' => $item->ticket_no,
                    'message' => "Incident {$item->ticket_no} updated to {$item->status}."
                ];
            }
        }

        if ($role === 'sfu') {
            $referrals = Referral::where('student_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($referrals as $item) {
                $notifications[] = [
                    'type' => 'Referral',
                    'ticket_no' => $item->referral_no,
                    'message' => "Referral {$item->referral_no} updated to {$item->status}."
                ];
            }

            $incidents = Incident::where('reporter_name', 'like', "%{$user->first_name}%")->where('updated_at', '>=', $since)->get();
            foreach ($incidents as $item) {
                $notifications[] = [
                    'type' => 'Incident',
                    'ticket_no' => $item->ticket_no,
                    'message' => "Incident {$item->ticket_no} updated to {$item->status}."
                ];
            }
        }

        return response()->json($notifications);
    }
}
