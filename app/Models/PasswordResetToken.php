<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    public $timestamps = false; // Your migration has no updated_at/created_at
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'email';
    public $incrementing = false;

    protected $fillable = ['email', 'token', 'created_at'];

    // Add expiry check
    public function isExpired()
    {
        return Carbon::parse($this->created_at)->addMinutes(10)->isPast(); // Optional: 10-min expiry
    }
}
