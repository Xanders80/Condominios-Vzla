<?php

namespace Database\Factories;

use App\Models\TowerSector;
use App\Models\Condominiums;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TowerSectorFactory extends Factory
{
    protected $model = TowerSector::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->word,
            'condominiums_id' => Condominiums::factory(),
        ];
    }
}
