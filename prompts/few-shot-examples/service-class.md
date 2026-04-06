# Few-Shot Example: Service Class

## Input
Create a service class for calculating condominium common expenses distribution based on coownership coefficients.

## Output

### `app/Services/CommonExpenseDistributionService.php`
```php
<?php

namespace App\Services;

use App\Models\CommonExpense;
use App\Models\Unit;
use App\Models\Receipt;
use App\Models\ReceiptConcept;
use Illuminate\Support\Facades\DB;

class CommonExpenseDistributionService
{
    /**
     * Distribute a common expense across all units based on coownership coefficients.
     *
     * @param CommonExpense $expense
     * @return array Summary of distribution
     */
    public function distribute(CommonExpense $expense): array
    {
        $totalAmount = $expense->amount;
        $reserveFundPercentage = 0.05;
        $reserveFund = $totalAmount * $reserveFundPercentage;
        $totalWithReserve = $totalAmount + $reserveFund;

        $units = Unit::where('is_active', true)
            ->whereNotNull('coownership_coefficient')
            ->where('coownership_coefficient', '>', 0)
            ->get();

        $totalCoefficient = $units->sum('coownership_coefficient');

        if ($totalCoefficient === 0) {
            throw new \InvalidArgumentException('No units with valid coefficients found');
        }

        DB::beginTransaction();
        try {
            $distributed = [];
            foreach ($units as $unit) {
                $unitAmount = ($unit->coownership_coefficient / $totalCoefficient) * $totalWithReserve;

                $receipt = Receipt::create([
                    'unit_id' => $unit->id,
                    'common_expense_id' => $expense->id,
                    'period' => $expense->period,
                    'amount' => round($unitAmount, 2),
                    'status' => 'pending',
                ]);

                ReceiptConcept::create([
                    'receipt_id' => $receipt->id,
                    'description' => $expense->description,
                    'amount' => round(($unit->coownership_coefficient / $totalCoefficient) * $totalAmount, 2),
                ]);

                ReceiptConcept::create([
                    'receipt_id' => $receipt->id,
                    'description' => 'Fondo de Reserva (5%)',
                    'amount' => round(($unit->coownership_coefficient / $totalCoefficient) * $reserveFund, 2),
                ]);

                $distributed[] = [
                    'unit_id' => $unit->id,
                    'unit_identifier' => $unit->identifier,
                    'coefficient' => $unit->coownership_coefficient,
                    'amount' => round($unitAmount, 2),
                ];
            }

            $expense->update(['is_distributed' => true, 'distributed_at' => now()]);
            DB::commit();

            return [
                'total_distributed' => round($totalWithReserve, 2),
                'units_count' => count($distributed),
                'reserve_fund' => round($reserveFund, 2),
                'details' => $distributed,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### `tests/Unit/CommonExpenseDistributionTest.php`
```php
<?php

namespace Tests\Unit;

use App\Models\CommonExpense;
use App\Models\Unit;
use App\Services\CommonExpenseDistributionService;
use Tests\TestCase;

class CommonExpenseDistributionTest extends TestCase
{
    public function test_distributes_expense_proportionally(): void
    {
        $unit1 = Unit::factory()->create(['coownership_coefficient' => 0.60]);
        $unit2 = Unit::factory()->create(['coownership_coefficient' => 0.40]);
        $expense = CommonExpense::factory()->create(['amount' => 1000]);

        $service = new CommonExpenseDistributionService();
        $result = $service->distribute($expense);

        $this->assertEquals(2, $result['units_count']);
        $this->assertEquals(1050.00, $result['total_distributed']); // 1000 + 5% reserve
        $this->assertEquals(50.00, $result['reserve_fund']);
    }

    public function test_throws_exception_when_no_valid_coefficients(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Unit::factory()->create(['coownership_coefficient' => null]);
        $expense = CommonExpense::factory()->create(['amount' => 1000]);

        $service = new CommonExpenseDistributionService();
        $service->distribute($expense);
    }
}
```
