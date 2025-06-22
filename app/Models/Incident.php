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

    /**
     * Keyword pools for determining incident level (English + Tagalog).
     */
    protected static $keywordLevels = [
        'High' => [
            'fight', 'punched', 'assault', 'threat', 'weapon', 'stabbed', 'knife', 'gun', 'explosion',
            'sinaksak', 'binaril', 'may patalim', 'banta', 'armas',
            'fire', 'burning', 'arson', 'smoke', 'flames', 'sunog', 'nasusunog', 'may apoy',
            'earthquake', 'flood', 'storm', 'typhoon', 'tornado', 'landslide', 'lightning',
            'lindol', 'baha', 'bagyo', 'kidlat', 'aftershock',
            'bomb', 'explosive', 'terrorist', 'riot', 'evacuation', 'lockdown', 'panic',
            'bomba', 'nakakatakot', 'nagsisigawan', 'gulo', 'emergency', 'may sumabog',
        ],

        'Normal' => [
            'late', 'disrespect', 'noise', 'argue', 'yelling', 'cheating', 'bullying',
            'late pumasok', 'maingay', 'nang-aasar', 'nangopya', 'sumigaw', 'nagsigawan',
            'nambubully', 'hindi sumunod', 'bastos', 'walang galang',
            'loitering', 'disobedience', 'shouting', 'mocking',
        ],

        'Low' => [
            'broke', 'vandalized', 'damaged', 'littering', 'graffiti',
            'basura', 'nagkalat', 'may sirang upuan', 'sirang gamit', 'kalat',
            'spilled', 'dirty', 'clogged', 'mess', 'nabasag', 'natapon', 'nakabasag',
            'nawala', 'na-misplace', 'naiwan', 'unattended bag', 'lost item',
        ],
    ];

    public function getLevelAttribute($value)
    {
        if ($value) return $value;

        $desc = strtolower($this->incident);

        foreach (self::$keywordLevels as $level => $words) {
            foreach ($words as $word) {
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/u', $desc)) {
                    return $level;
                }
            }
        }

        return 'Normal'; // fallback
    }

    /**
     * Return color based on level.
     */
    public function getLevelColorAttribute()
    {
        return match ($this->level) {
            'High' => 'red',
            'Normal' => 'orange',
            'Low' => 'green',
            default => 'gray',
        };
    }
}
