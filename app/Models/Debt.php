<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'unit_id',
        'receipt_id',
        'amount',
        'status',
        'due_date',
        'grace_period_days',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'grace_period_days' => 'integer',
    ];

    /**
     * Get the unit that owns the debt.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the receipt associated with the debt.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    /**
     * Get the interest calculations for this debt.
     */
    public function interestCalculations()
    {
        return $this->hasMany(InterestCalculation::class);
    }
}
