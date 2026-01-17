<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\InterestRate;
use App\Models\InterestCalculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InterestCalculationService
{
    /**
     * Process interests for all active debts.
     */
    public function processInterests(): int
    {
        $processedCount = 0;
        $activeDebts = Debt::whereIn('status', ['current', 'pre_delinquent', 'delinquent'])
            ->where('due_date', '<', Carbon::today())
            ->get();

        foreach ($activeDebts as $debt) {
            if ($this->calculateForDebt($debt)) {
                $processedCount++;
            }
        }

        return $processedCount;
    }

    /**
     * Calculate and apply interest for a specific debt.
     */
    public function calculateForDebt(Debt $debt): ?InterestCalculation
    {
        // 1. Check Grace Period
        $today = Carbon::today();
        if ($today->lte($debt->due_date)) {
            return null;
        }

        $daysOverdue = $debt->due_date->diffInDays($today);
        if ($daysOverdue <= $debt->grace_period_days) {
            return null;
        }

        // 2. Get Applicable Interest Rate
        $rate = $this->getApplicableRate();
        if (!$rate) {
            return null;
        }

        // 3. Simple Interest Calculation
        $dailyRate = ($rate->percentage / 100) / 365;
        $interestAmount = $debt->amount * $dailyRate;
        return DB::transaction(function () use ($debt, $interestAmount, $rate) {
            if ($debt->status === 'current') {
                $debt->status = 'delinquent';
                $debt->save();
            }

            return InterestCalculation::create([
                'debt_id' => $debt->id,
                'interest_amount' => $interestAmount,
                'cumulative_capital' => $debt->amount,
                'calculation_date' => Carbon::today(),
                'rate_applied' => $rate->percentage,
            ]);
        });
    }

    /**
     * Get the current active legal/official rate.
     */
    protected function getApplicableRate(): ?InterestRate
    {
        $today = Carbon::today()->toDateString();
        $rate = InterestRate::where('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->latest('start_date')
            ->first();

        return $rate;
    }
}
