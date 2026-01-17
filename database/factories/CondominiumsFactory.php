<?php

namespace Database\Factories;

use App\Models\Condominiums;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CondominiumsFactory extends Factory
{
    protected $model = Condominiums::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'rif' => 'J-' . $this->faker->numberBetween(10000000, 99999999) . '-' . $this->faker->numberBetween(0, 9),
            'active' => true,
        ];
    }
}
