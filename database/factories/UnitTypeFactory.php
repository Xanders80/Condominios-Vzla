<?php

namespace Database\Factories;

use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UnitTypeFactory extends Factory
{
    protected $model = UnitType::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->word,
        ];
    }
}
