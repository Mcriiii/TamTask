<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referral_no',
        'date_reported',
        'user_id',
        'level',
        'student_name',
        'date_to_see',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
