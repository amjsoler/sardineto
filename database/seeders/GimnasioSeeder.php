<?php

namespace Database\Seeders;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GimnasioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gimnasio::factory()->count(5)->create([
            "propietario" => User::first()->id
        ]);
    }
}
