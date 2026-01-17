<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterestRate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'rate_type',
        'percentage',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
