<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommonAreaBooking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'unit_id',
        'common_area_id',
        'start_time',
        'end_time',
        'total_amount',
        'currency',
        'exchange_rate',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    /**
     * Get the unit that made the booking.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the common area being booked.
     */
    public function commonArea()
    {
        return $this->belongsTo(CommonArea::class);
    }
}
