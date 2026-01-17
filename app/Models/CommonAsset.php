<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommonAsset extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'condominiums_id',
        'name',
        'description',
        'location',
        'status',
    ];

    /**
     * Get the condominium that owns the asset.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominiums::class, 'condominiums_id');
    }

    /**
     * Get the incident reports for this asset.
     */
    public function incidentReports()
    {
        return $this->hasMany(IncidentReport::class);
    }
}
