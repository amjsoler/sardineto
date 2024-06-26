<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gimnasio>
 */
class GimnasioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nombre" => $this->faker->name,
            "descripcion" => $this->faker->paragraph(2),
            "logo" => $this->faker->imageUrl,
            "direccion" => $this->faker->address,
        ];
    }
}
