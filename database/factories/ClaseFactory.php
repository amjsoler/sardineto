<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clase>
 */
class ClaseFactory extends Factory
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
            "descripcion" => $this->faker->paragraph(3),
            "fechayhora" => $this->faker->dateTime,
            "plazas" => $this->faker->numberBetween(1, 20),
        ];
    }
}
