<?php

namespace Database\Seeders;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use Illuminate\Database\Seeder;

class TarifaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tarifa::factory()->count(5)->create([
            "gimnasio" => Gimnasio::find(1)->id
        ]);
    }
}
