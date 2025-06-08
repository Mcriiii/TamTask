<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}
