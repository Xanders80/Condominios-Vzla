<?php

namespace App\Services\Backend;

use App\Models\Debt;
use App\Models\InterestCalculation;
use App\Models\InterestRate;
use App\Services\BaseService;
use Carbon\Carbon;

class InterestService extends BaseService
{
    /**
     * Calculate and apply interest to a specific debt.
     */
    public function calculateInterest(Debt $debt): array
    {
        if ($debt->status === 'paid') {
            return $this->error('Cannot calculate interest for paid debts.');
        }

        $now = Carbon::now();
        $dueDate = Carbon::parse($debt->due_date)->addDays($debt->grace_period_days);

        if ($now->lte($dueDate)) {
            return $this->success('Debt is not yet overdue.');
        }

        return $this->executeTransaction(function () use ($debt, $now, $dueDate) {
            // Get current active interest rate for the condominium
            $rate = InterestRate::where('condominium_id', $debt->unit->condominiums_id)
                ->where('active', true)
                ->first();

            if (!$rate) {
                return $this->error('No active interest rate found for this condominium.');
            }

            $daysOverdue = $now->diffInDays($dueDate);

            // Basic formula: amount * (rate / 100) * (days / 30)
            // This is a simplified monthly simple interest.
            // Depending on local laws (LPH), this might need adjustment to compound interest.
            $monthlyRate = $rate->rate / 100;
            $interestAmount = $debt->amount * $monthlyRate * ($daysOverdue / 30);

            $calculation = InterestCalculation::create([
                'debt_id' => $debt->id,
                'interest_rate_id' => $rate->id,
                'amount_calculated' => $interestAmount,
                'days_overdue' => $daysOverdue,
                'calculation_date' => $now,
            ]);

            return $this->success('Interest calculated successfully.', [
                'amount' => $interestAmount,
                'days' => $daysOverdue
            ]);
        }, 'Interest calculation failed');
    }
}
