<?php

namespace Database\Seeders;

use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuscripcionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Suscripcion::factory()->count(5)->create([
            "usuario" => User::find(1),
            "gimnasio" => Gimnasio::find(1),
            "tarifa" => Gimnasio::find(1)->tarifas()->first(),
        ]);
    }
}
