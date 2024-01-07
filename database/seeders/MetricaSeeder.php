<?php

namespace Database\Seeders;

use App\Models\Metrica;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetricaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Metrica::factory()->count(10)->create([
            "usuario" => User::find(1)
        ]);
    }
}
