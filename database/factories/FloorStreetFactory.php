<?php

namespace Database\Factories;

use App\Models\FloorStreet;
use App\Models\TowerSector;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FloorStreetFactory extends Factory
{
    protected $model = FloorStreet::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->word,
            'tower_sector_id' => TowerSector::factory(),
        ];
    }
}
