<?php

namespace Database\Seeders;

use App\Models\Articulo;
use App\Models\Gimnasio;
use Illuminate\Database\Seeder;

class ArticuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Articulo::factory()->count(20)->create([
            "gimnasio" => Gimnasio::find(1)
        ]);
    }
}
