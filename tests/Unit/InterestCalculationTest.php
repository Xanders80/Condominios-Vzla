<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Unit;
use App\Models\Debt;
use App\Models\InterestRate;
use App\Services\InterestCalculationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InterestCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InterestCalculationService();

        // Setup a standard legal rate (12% annual = 1% monthly approx)
        InterestRate::create([
            'rate_type' => 'legal',
            'percentage' => 12.00,
            'start_date' => Carbon::today()->subYear(),
        ]);
    }

    public function test_it_calculates_daily_interest_correctly()
    {
        $unit = Unit::factory()->create();
        $debt = Debt::create([
            'unit_id' => $unit->id,
            'amount' => 1000.00,
            'status' => 'current',
            'due_date' => Carbon::today()->subDays(10),
            'grace_period_days' => 0,
        ]);

        $calculation = $this->service->calculateForDebt($debt);

        $this->assertNotNull($calculation);
        // 1000 * (12/100/365) = 0.328... approx 0.33
        $this->assertEquals(0.33, round($calculation->interest_amount, 2));
        $this->assertEquals('delinquent', $debt->fresh()->status);
    }

    public function test_it_respects_grace_period()
    {
        $unit = Unit::factory()->create();
        $debt = Debt::create([
            'unit_id' => $unit->id,
            'amount' => 1000.00,
            'status' => 'current',
            'due_date' => Carbon::today()->subDays(5),
            'grace_period_days' => 7, // Still in grace period
        ]);

        $calculation = $this->service->calculateForDebt($debt);

        $this->assertNull($calculation);
        $this->assertEquals('current', $debt->fresh()->status);
    }
}
