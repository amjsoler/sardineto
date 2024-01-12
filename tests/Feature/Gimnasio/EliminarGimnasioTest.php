<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminarGimnasioTest extends TestCase
{
    use RefreshDatabase;

    public function test_eliminar_gimnasio_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-gimnasio", 1),
            []);

        $response->assertStatus(401);
    }

    public function test_eliminar_gimnasio_sin_verificar_cuenta_usuario()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->deleteJson(route("eliminar-gimnasio", 1),
            []);

        $response->assertStatus(460);
    }

    public function test_eliminar_gimnasio_no_authorization()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario2
        ]);

        //Comprobamos sin ser nada que da 403
        $response = $this->deleteJson(route("eliminar-gimnasio", $gimnasio->id), []);
        $response->assertStatus(403);

        //Ahora nos damos de alta como administrador para comprobar que sigue petando
        $gimnasio->administradores()->attach($usuario1->id);
        $response = $this->deleteJson(route("eliminar-gimnasio", $gimnasio->id), []);
        $response->assertStatus(403);

        //Siendo propietario debería dar 422 sin payload
        $this->actingAs($usuario2);
        $response = $this->deleteJson(route("eliminar-gimnasio", $gimnasio->id), []);
        $response->assertStatus(200);

        //Comprobamos que ya no está
        $this->assertEquals(0, Gimnasio::all()->count());
    }

    public function test_eliminar_gimnasio_que_no_existe()
    {
        $usuario1 = User::factory()->create();
        $this->actingAs($usuario1);

        $response = $this->deleteJson(route("eliminar-gimnasio", 999));
        $response->assertStatus(404);
    }
}
