<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Metrica>
 */
class MetricaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "peso" => $this->faker->randomFloat(2, 70, 95),
            "porcentaje_graso" => $this->faker->randomFloat(2, 4, 30),
        ];
    }
}
