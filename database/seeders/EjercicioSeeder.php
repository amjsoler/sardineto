<?php

namespace Database\Seeders;

use App\Models\Ejercicio;
use App\Models\Gimnasio;
use Illuminate\Database\Seeder;

class EjercicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ejercicio::factory()->count(10)->create([
            "gimnasio" => Gimnasio::find(1)
        ]);
    }
}
