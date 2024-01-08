<?php

namespace Database\Seeders;

use App\Models\Ejercicio;
use App\Models\EjercicioUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;

class EjercicioUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EjercicioUsuario::factory()->count(10)->create([
            "usuario" => User::find(1),
            "ejercicio" => Ejercicio::find(1)
        ]);
    }
}
