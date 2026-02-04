<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'common_expense_id',
        'unit_id',
        'issue_date',
        'due_date',
        'total_amount',
        'coownership_coefficient',
        'status',
        'receipt_number',
        'concepts_breakdown',
        'qr_verification_hash',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'coownership_coefficient' => 'decimal:4',
        'concepts_breakdown' => 'json',
    ];

    /**
     * Get the unit associated with the receipt.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the common expense block associated with the receipt.
     */
    public function commonExpense()
    {
        return $this->belongsTo(CommonExpense::class);
    }

    /**
     * Get the concepts for the receipt.
     */
    public function concepts()
    {
        return $this->hasMany(ReceiptConcept::class);
    }

    /**
     * Check if the receipt is paid.
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Get the debts associated with the receipt.
     */
    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}
