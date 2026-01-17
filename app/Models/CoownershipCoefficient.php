<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoownershipCoefficient extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'coefficient',
        'start_date',
    ];

    protected $casts = [
        'coefficient' => 'decimal:4',
        'start_date' => 'date',
    ];

    /**
     * Get the unit that owns this coefficient record.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
