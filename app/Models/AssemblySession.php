<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssemblySession extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'condominiums_id',
        'title',
        'agenda',
        'session_date',
        'status',
    ];

    protected $casts = [
        'session_date' => 'datetime',
    ];

    /**
     * Get the condominium that owns the assembly session.
     */
    public function condominium()
    {
        return $this->belongsTo(Condominiums::class, 'condominiums_id');
    }

    /**
     * Get the motions proposed in this session.
     */
    public function motions()
    {
        return $this->hasMany(Motion::class);
    }
}
