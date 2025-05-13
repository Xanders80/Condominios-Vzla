<?php

namespace Database\Factories;

use App\Models\DwellerType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DwellerTypeFactory extends Factory
{
    protected $model = DwellerType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Assuming the DwellerType has a 'name' field
        ];
    }
}
