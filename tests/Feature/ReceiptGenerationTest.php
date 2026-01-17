<?php

namespace Tests\Feature;

use App\Models\Condominiums;
use App\Models\Unit;
use App\Models\Dweller;
use App\Models\TowerSector;
use App\Models\UnitType;
use App\Services\ReceiptGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptGenerationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test mass receipt generation logic.
     */
    public function test_mass_receipt_generation_calculates_correctly()
    {
        // Setup Condominium
        $condo = Condominiums::create([
            'name' => 'Test Condo',
            'rif' => 'J-12345678-9',
        ]);

        $tower = TowerSector::create([
            'name' => 'Torre A',
            'condominiums_id' => $condo->id,
        ]);

        $unitType = UnitType::create(['name' => 'Apartamento']);

        $dweller = Dweller::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'document_id' => 99999999,
            'email' => 'john@example.com',
        ]);

        // Create 2 units with different coefficients
        $unit1 = Unit::create([
            'name' => '101',
            'tower_sector_id' => $tower->id,
            'unit_type_id' => $unitType->id,
            'dweller_id' => $dweller->id,
            'coefficient' => 0.6000,
            'status' => true,
        ]);

        $unit2 = Unit::create([
            'name' => '102',
            'tower_sector_id' => $tower->id,
            'unit_type_id' => $unitType->id,
            'dweller_id' => $dweller->id,
            'coefficient' => 0.4000,
            'status' => true,
        ]);

        $service = new ReceiptGenerationService();
        $expenses = [
            ['name' => 'Mantenimiento Elevador', 'amount' => 1000],
            ['name' => 'Jardineria', 'amount' => 500],
        ];

        $receipts = $service->generateMassReceipts($condo, $expenses, '2026-01-01', '2026-01-15');

        $this->assertCount(2, $receipts);

        // Total expenses = 1500
        // Reserve Fund (5%) = 75
        // Grand Total = 1575

        // Unit 1 (60%) = 1575 * 0.6 = 945
        $this->assertEquals(945, $receipts[0]->total_amount);

        // Unit 2 (40%) = 1575 * 0.4 = 630
        $this->assertEquals(630, $receipts[1]->total_amount);

        // Verify concepts for Unit 1
        $this->assertCount(3, $receipts[0]->concepts); // 2 expenses + 1 reserve fund
        $this->assertEquals(45, $receipts[0]->concepts->where('concept_name', 'Fondo de Reserva')->first()->amount); // 75 * 0.6 = 45
    }
}
