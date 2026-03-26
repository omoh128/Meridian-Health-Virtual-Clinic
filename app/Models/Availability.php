<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'max_per_slot',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function dayName(int $day): string
    {
        return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$day];
    }
}
