<?php

namespace Database\Factories;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeFactory extends Factory
{
    protected $model = DocumentType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Assuming the DocumentType has a 'name' field
        ];
    }
}
