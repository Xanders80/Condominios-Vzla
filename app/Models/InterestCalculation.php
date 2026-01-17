<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InterestCalculation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'debt_id',
        'interest_amount',
        'cumulative_capital',
        'calculation_date',
        'rate_applied',
    ];

    protected $casts = [
        'interest_amount' => 'decimal:2',
        'cumulative_capital' => 'decimal:2',
        'calculation_date' => 'date',
        'rate_applied' => 'decimal:2',
    ];

    /**
     * Get the debt associated with the calculation.
     */
    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
