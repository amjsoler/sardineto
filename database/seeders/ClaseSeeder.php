<?php

namespace Database\Seeders;

use App\Models\Clase;
use App\Models\Gimnasio;
use Illuminate\Database\Seeder;

class ClaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clase::factory()->count(100)->create([
            "gimnasio" => Gimnasio::first()->id
        ]);
    }
}
