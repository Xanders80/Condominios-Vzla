<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'motion_id',
        'unit_id',
        'vote',
    ];

    /**
     * Get the motion being voted on.
     */
    public function motion()
    {
        return $this->belongsTo(Motion::class);
    }

    /**
     * Get the unit that cast the vote.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
