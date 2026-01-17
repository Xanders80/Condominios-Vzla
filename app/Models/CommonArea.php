<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommonArea extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'condominiums_id',
        'name',
        'description',
        'booking_fee',
        'max_occupancy',
        'is_active',
    ];

    protected $casts = [
        'booking_fee' => 'decimal:2',
        'max_occupancy' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the condominium that owns the common area.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominiums::class, 'condominiums_id');
    }

    /**
     * Get the bookings for this common area.
     */
    public function bookings()
    {
        return $this->hasMany(CommonAreaBooking::class);
    }

    /**
     * Get the incident reports for this common area.
     */
    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }
}
