<?php

namespace Tests\Unit;

use App\Services\ReceiptGenerationService;
use PHPUnit\Framework\TestCase;

class ReceiptCalculationTest extends TestCase
{
    /**
     * Test calculation logic for unit totals.
     */
    public function test_calculate_unit_total_logic()
    {
        $service = new ReceiptGenerationService();

        $totalExpenses = 1500;
        $reserveFundAmount = 75; // 5% of 1500
        $coefficient = 0.60;

        $result = $service->calculateUnitTotal($totalExpenses, $reserveFundAmount, $coefficient);

        // (1500 + 75) * 0.60 = 1575 * 0.60 = 945
        $this->assertEquals(945, $result);
    }

    /**
     * Test calculation logic with zero coefficient.
     */
    public function test_calculate_unit_total_with_zero_coefficient()
    {
        $service = new ReceiptGenerationService();
        $this->assertEquals(0, $service->calculateUnitTotal(1500, 75, 0));
    }

    /**
     * Test calculation logic with null coefficient.
     */
    public function test_calculate_unit_total_with_null_coefficient()
    {
        $service = new ReceiptGenerationService();
        $this->assertEquals(0, $service->calculateUnitTotal(1500, 75, null));
    }
}
