<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptConcept extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_id',
        'concept_name',
        'amount',
        'coefficient_applied',
        'description',
        'legal_basis_article',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'coefficient_applied' => 'decimal:4',
    ];

    /**
     * Get the receipt that owns the concept.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
