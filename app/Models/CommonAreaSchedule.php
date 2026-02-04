<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CommonAreaSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'common_area_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the common area that owns the schedule.
     */
    public function commonArea()
    {
        return $this->belongsTo(CommonArea::class);
    }
}
