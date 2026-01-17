<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentReport extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'unit_id',
        'common_area_id',
        'common_asset_id',
        'description',
        'priority',
        'status',
    ];

    /**
     * Get the unit associated with the report.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the common area associated with the report.
     */
    public function commonArea()
    {
        return $this->belongsTo(CommonArea::class);
    }

    /**
     * Get the common asset associated with the report.
     */
    public function commonAsset()
    {
        return $this->belongsTo(CommonAsset::class);
    }

    /**
     * Get the work orders generated from this incident.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get the attachments for the incident report.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
