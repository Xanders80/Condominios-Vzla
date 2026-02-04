<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommonExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'condominium_id',
        'period',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'period' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function condominium()
    {
        return $this->belongsTo(Condominiums::class, 'condominium_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
