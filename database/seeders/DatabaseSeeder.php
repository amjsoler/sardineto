<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            "name" => "Jorge",
            "email" => "amjsoler@gmail.com",
            "password" => Hash::make("jas12345"),
            "email_verified_at" => now()
        ]);

        User::factory()->count(10)->create();

        $this->call([
            GimnasioSeeder::class,
            ClaseSeeder::class
        ]);
    }
}
