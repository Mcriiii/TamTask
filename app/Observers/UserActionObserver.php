<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Models\LostFound;

class UserActionObserver
{
   public function updated($model)
    {
         if ($model instanceof LostFound) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated LostFound ID: ' . $model->id,
            ]);
        }

        if ($model instanceof Incident) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Incident ID: ' . $model->id,
            ]);
        }
        if ($model instanceof Complaint) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated Complaint ID: ' . $model->id,
            ]);
        }
        if ($model instanceof User) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated User ID: ' . $model->id,
            ]);
        }
    }

    public function deleted($model)
    {
        if ($model instanceof LostFound) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted LostFound ID: ' . $model->id,
            ]);
        }

        if ($model instanceof Incident) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Incident ID: ' . $model->id,
            ]);
        }
        if ($model instanceof Complaint) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted Complaint ID: ' . $model->id,
            ]);
        }
        if ($model instanceof User) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted User ID: ' . $model->id,
            ]);
        }
    }
}
