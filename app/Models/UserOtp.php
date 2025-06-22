<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at', 'verified'];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
