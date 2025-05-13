<?php

namespace Database\Factories;

use App\Models\Dweller;
use App\Models\DocumentType; // Import the DocumentType model
use App\Models\DwellerType; // Import the DwellerType model
use Illuminate\Database\Eloquent\Factories\Factory;

class DwellerFactory extends Factory
{
    protected $model = Dweller::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'document_type_id' => DocumentType::factory(), // Use a factory for DocumentType
            'document_id' => $this->faker->unique()->numberBetween(1000001, 9999999),
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'cell_phone_number' => $this->faker->phoneNumber,
            'dweller_type_id' => DwellerType::factory(), // Use a factory for DwellerType
            'observations' => $this->faker->sentence,
        ];
    }
}
