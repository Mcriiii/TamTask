<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referral_no',
        'date_reported',
        'level',
        'student_name',  // added
        'date_to_see',
        'status',
    ];
}
