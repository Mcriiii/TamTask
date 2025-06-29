<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Incident;
use App\Models\Complaint;
use App\Models\LostFound;
use App\Models\Violation;
use App\Models\ActivityLog;
use App\Models\Certificate;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;

class UserActionObserver
{
   public function updated($model)
    {
         if ($model instanceof LostFound) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated LostFound Ticket No.: ' . $model->ticket_no,
            ]);
        }

        if ($model instanceof Incident) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Incident Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Complaint) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Complaint Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Violation) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Violation Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Certificate) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Certificate Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Referral) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Referral Ticket No. ' . $model->referral_no,
            ]);
        }
        if ($model instanceof User) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated User Ticket No. ' . $model->ticket_no,
            ]);
        }
    }

    public function deleted($model)
    {
        if ($model instanceof LostFound) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted LostFound Ticket No. ' . $model->ticket_no,
            ]);
        }

        if ($model instanceof Incident) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Incident Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Complaint) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Complaint Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Violation) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Violation Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Certificate) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Certificate Ticket No. ' . $model->ticket_no,
            ]);
        }
        if ($model instanceof Referral) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Referral Ticket No. ' . $model->referral_no,
            ]);
        }
        if ($model instanceof User) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted User Ticket No. ' . $model->ticket_no,
            ]);
        }
    }
}
