<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BcvExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate_date',
        'official_rate',
        'parallel_rate',
        'used_for_calculations',
        'fetched_at',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'official_rate' => 'decimal:4',
        'parallel_rate' => 'decimal:4',
        'used_for_calculations' => 'decimal:4',
        'fetched_at' => 'datetime',
    ];

    /**
     * Get the most recent exchange rate.
     */
    public static function latestRate()
    {
        return self::orderBy('rate_date', 'desc')->first();
    }
}
