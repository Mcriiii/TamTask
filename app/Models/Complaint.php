<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Complaint extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'ticket_no',
        'user_id',
        'reporter_name',
        'student_no',
        'date_reported',
        'yearlvl_degree',
        'subject',
        'meeting_schedule',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
