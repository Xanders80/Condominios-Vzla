<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\UnitType;
use App\Models\TowerSector;
use App\Models\FloorStreet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'unit_type_id' => UnitType::factory(),
            'tower_sector_id' => TowerSector::factory(),
            'floor_street_id' => function (array $attributes) {
                return FloorStreet::factory()->create([
                    'tower_sector_id' => $attributes['tower_sector_id']
                ])->id;
            },
            'name' => $this->faker->numerify('Unit-###'),
            'status' => true,
        ];
    }
}
