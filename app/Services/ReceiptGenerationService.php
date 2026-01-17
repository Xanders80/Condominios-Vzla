<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\ReceiptConcept;
use App\Models\Unit;
use App\Models\Condominiums; // Model name from App/Models/Condominiums.php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceiptGenerationService
{
    /**
     * Generate receipts for all units in a condominium for a specific month.
     *
     * @param Condominiums $condominium
     * @param array $expenses Array of ['name' => string, 'amount' => float]
     * @param string $issueDate YYYY-MM-DD
     * @param string $dueDate YYYY-MM-DD
     */
    public function generateMassReceipts(Condominiums $condominium, array $expenses, $issueDate, $dueDate)
    {
        return DB::transaction(function () use ($condominium, $expenses, $issueDate, $dueDate) {
            $units = Unit::whereHas('towerSector', function ($q) use ($condominium) {
                $q->where('condominiums_id', $condominium->id);
            })->get();

            $totalExpenses = collect($expenses)->sum('amount');
            $reserveFundPercentage = 0.05; // 5% mandatory by LPH Art. 36
            $reserveFundAmount = $totalExpenses * $reserveFundPercentage;

            $generatedReceipts = [];

            foreach ($units as $unit) {
                $unitTotal = $this->calculateUnitTotal($totalExpenses, $reserveFundAmount, $unit->coefficient);

                $receipt = Receipt::create([
                    'unit_id' => $unit->id,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'total_amount' => $unitTotal,
                    'coownership_coefficient' => $unit->coefficient,
                    'status' => 'pending',
                    'receipt_number' => $this->generateReceiptNumber($condominium, $unit, $issueDate),
                    'qr_verification_hash' => hash('sha256', Str::uuid() . $unit->id . $issueDate),
                ]);

                // Create main maintenance concept (aggregated for this implementation example)
                foreach ($expenses as $expense) {
                    ReceiptConcept::create([
                        'receipt_id' => $receipt->id,
                        'concept_name' => $expense['name'],
                        'amount' => $expense['amount'] * $unit->coefficient,
                        'coefficient_applied' => $unit->coefficient,
                        'description' => "Proportional share of " . $expense['name'],
                    ]);
                }

                // Create Reserve Fund concept
                ReceiptConcept::create([
                    'receipt_id' => $receipt->id,
                    'concept_name' => 'Fondo de Reserva',
                    'amount' => $reserveFundAmount * $unit->coefficient,
                    'coefficient_applied' => $unit->coefficient,
                    'description' => "5% Fondo de Reserva obligatorio (Art. 36 LPH)",
                    'legal_basis_article' => 36,
                ]);

                $generatedReceipts[] = $receipt;
            }

            return $generatedReceipts;
        });
    }

    /**
     * Calculate the total amount for a unit.
     */
    public function calculateUnitTotal($totalExpenses, $reserveFundAmount, $coefficient)
    {
        return ($totalExpenses + $reserveFundAmount) * ($coefficient ?? 0);
    }

    /**
     * Generate a unique receipt number.
     */
    protected function generateReceiptNumber(Condominiums $condominium, Unit $unit, $date)
    {
        $prefix = strtoupper(substr($condominium->name, 0, 3));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        $unique = strtoupper(Str::random(4));

        return "{$prefix}-{$year}{$month}-{$unit->name}-{$unique}";
    }
}
