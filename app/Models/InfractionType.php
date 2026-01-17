<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfractionType extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'condominiums_id',
        'name',
        'penalty_amount',
        'legal_basis',
    ];

    protected $casts = [
        'penalty_amount' => 'decimal:2',
    ];

    /**
     * Get the condominium that owns this infraction type.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominiums::class, 'condominiums_id');
    }

    /**
     * Get the sanctions associated with this type.
     */
    public function sanctions()
    {
        return $this->hasMany(Sanction::class);
    }
}
