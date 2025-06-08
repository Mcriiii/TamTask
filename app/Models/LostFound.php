<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class LostFound extends Model
{
   use HasFactory, Notifiable;

    protected $fillable = [
        'ticket_no',
        'reporter_name',
        'email',
        'date_reported',
        'location_found',
        'item_type',
        'description',
        'status',
    ];
}
