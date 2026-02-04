<?php

namespace App\Services\Backend;

use App\Models\CommonExpense;
use App\Models\Receipt;
use App\Models\ReceiptConcept;
use App\Models\Unit;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseEngine extends BaseService
{
    /**
     * Distribute a common expense block among all units of a condominium.
     */
    public function distribute(CommonExpense $expense): array
    {
        if ($expense->status !== 'draft') {
            return $this->error('Only draft expenses can be distributed.');
        }

        return $this->executeTransaction(function () use ($expense) {
            // 1. Get all units for this condominium with their coefficients
            $units = Unit::where('condominiums_id', $expense->condominium_id)->get();

            if ($units->isEmpty()) {
                throw new \Exception('No units found for this condominium.');
            }

            // 2. Clear existing receipts if any (re-distribution)
            $expense->receipts()->delete();

            $totalDistributed = 0;

            foreach ($units as $unit) {
                // Calculate amount based on coownership_coefficient (aliquot)
                // total_amount * coefficient
                $amount = $expense->total_amount * ($unit->coownership_coefficient ?? 0);

                if ($amount <= 0) continue;

                $receipt = Receipt::create([
                    'common_expense_id' => $expense->id,
                    'unit_id' => $unit->id,
                    'issue_date' => now(),
                    'due_date' => now()->addDays(15), // Default due date
                    'total_amount' => $amount,
                    'coownership_coefficient' => $unit->coownership_coefficient,
                    'status' => 'pending',
                    'receipt_number' => $this->generateReceiptNumber($expense, $unit),
                ]);

                // Create a basic concept for the receipt
                ReceiptConcept::create([
                    'receipt_id' => $receipt->id,
                    'concept_name' => 'Gasto Común - Periodo ' . $expense->period->format('m/Y'),
                    'amount' => $amount,
                    'coefficient_applied' => $unit->coownership_coefficient,
                    'description' => 'Distribución proporcional del gasto del periodo.',
                ]);

                $totalDistributed += $amount;
            }

            return $this->success('Distribution completed successfully.', [
                'total_distributed' => $totalDistributed,
                'count' => $units->count()
            ]);
        }, 'Expense distribution failed');
    }

    /**
     * Generate a unique receipt number.
     */
    protected function generateReceiptNumber(CommonExpense $expense, Unit $unit): string
    {
        return 'REC-' . $expense->period->format('Ym') . '-' . Str::upper(Str::random(4)) . '-' . $unit->id;
    }
}
