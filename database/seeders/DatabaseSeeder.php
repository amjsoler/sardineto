<?php

namespace Database\Seeders;

use App\Models\Articulo;
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
        $jorge = User::create([
            "name" => "Jorge",
            "email" => "amjsoler@gmail.com",
            "password" => Hash::make("jas12345"),
            "email_verified_at" => now()
        ]);

        User::factory()->count(10)->create();

        $this->call([
            GimnasioSeeder::class,
            ClaseSeeder::class,
            TarifaSeeder::class,
            SuscripcionSeeder::class,
            ArticuloSeeder::class,
            EjercicioSeeder::class,
            MetricaSeeder::class
        ]);

        //Hacemos que Jorge esté invitado en el gimnasio 1
        $gim1 = Gimnasio::find(1);
        $gim1->usuariosInvitados()->attach($jorge);

        //Ahora compramos unos cuantos artículos con el usuario de pruebas id:1
        $art1 = Articulo::find(1);
        $art2 = Articulo::find(2);
        $art3 = Articulo::find(3);

        $user = User::find(1);
        $gimnasio = Gimnasio::find(1);

        $user->historialDeCompras()->attach($art1, ["gimnasio" => $gimnasio->id]);
        $user->historialDeCompras()->attach($art2, ["gimnasio" => $gimnasio->id]);
        $user->historialDeCompras()->attach($art3, ["gimnasio" => $gimnasio->id]);
        $user->historialDeCompras()->attach($art1, ["gimnasio" => $gimnasio->id]);
    }
}
