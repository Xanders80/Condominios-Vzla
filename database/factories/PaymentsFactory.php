<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payments>
 */
class PaymentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->text(),
            'dweller_id' => fake()->text(),
            'banks_id' => fake()->text(),
            'condominiums_id' => fake()->text(),
            'ways_to_pays_id' => fake()->text(),
            'nro_confirmation' => fake()->text(),
            'amount' => fake()->text(),
            'date_pay' => $this->faker->date(),
            'date_confirm' => $this->faker->date(),
            'conciliated' => fake()->text(),
            'observations' => fake()->text(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
            'deleted_at' => $this->faker->dateTime(),
        ];
    }
}
