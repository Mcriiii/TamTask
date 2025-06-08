<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referral_no',
        'date_reported',
        'level',
        'date_to_see',
        'role',
        'status',
    ];
}
