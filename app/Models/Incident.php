<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'ticket_no',
        'incident',
        'reporter_name',
        'level',
        'date_reported',
        'reporter_role',
        'status',
    ];

    public function getLevelAttribute($value)
    {
        if ($value) return $value; // use existing level if stored

        $desc = strtolower($this->incident);
        $highKeywords = ['fight', 'punched', 'assault', 'threat'];
        $lowKeywords = ['broke', 'vandalized', 'damaged'];
        $normalKeywords = ['late', 'disrespect', 'noise'];

        foreach ($highKeywords as $word) {
            if (strpos($desc, $word) !== false) return 'High';
        }
        foreach ($lowKeywords as $word) {
            if (strpos($desc, $word) !== false) return 'Low';
        }
        foreach ($normalKeywords as $word) {
            if (strpos($desc, $word) !== false) return 'Normal';
        }
        return 'Normal';
    }

    public function getLevelColorAttribute()
    {
        return match ($this->level) {
            'High' => 'red',
            'Low' => 'green',
            'Normal' => 'orange',
            default => 'gray',
        };
    }
}
