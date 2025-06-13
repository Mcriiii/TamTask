<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Certificate extends Model
{
   use HasFactory, Notifiable;

    protected $fillable = [
        'ticket_no',
        'requester_name',
        'email',
        'student_no',
        'yearlvl_degree',
        'date_requested',
        'purpose',
    ];
}
