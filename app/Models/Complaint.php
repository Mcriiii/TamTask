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
        'reporter_name',
        'student_no',
        'date_reported',
        'yearlvl_degree',
        'subject',
    ];
}
