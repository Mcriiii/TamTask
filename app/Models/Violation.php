<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Violation extends Model
{
    protected $fillable = [
        'violation_no',
        'full_name',
        'student_no',
        'student_email',
        'date_reported',
        'yearlvl_degree',
        'offense',
        'level',
        'status',
        'action_taken',
        'user_id',
        'escalation_resolved'
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
