<?php

namespace Tests\Feature\Ejercicio;

use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EliminarEjercicioTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;
    protected $gimnasio;
    protected $gimnasio2;
    protected $ejercicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarioInvitado = User::factory()->create();
        $this->usuarioInvitadoYAceptado = User::factory()->create();
        $this->administrador = User::factory()->create();
        $this->propietario = User::factory()->create();

        $this->gimnasio = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitado);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoYAceptado, [
            "invitacion_aceptada" => true
        ]);
        $this->ejercicio = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio]);
        $this->gimnasio->administradores()->attach($this->administrador);
    }

    public function test_eliminar_ejercicio_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-ejercicio", ["gimnasio" => $this->gimnasio->id, "ejercicio" => $this->ejercicio->id]));
        $response->assertStatus(401);
    }
    public function test_eliminar_ejercicio_sin_verificar_cuenta()
    {
        $usuarioSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuarioSinVerificar);

        $response = $this->deleteJson(route("eliminar-ejercicio", ["gimnasio" => $this->gimnasio->id, "ejercicio" => $this->ejercicio->id]));
        $response->assertStatus(460);
    }
    public function test_eliminar_ejercicio_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        $this->ejercicio = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio]);

        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        //Comprobar ejercicio pertenece a gim
        $this->ejercicio = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio]);

        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio2->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);
    }

    public function test_eliminar_ejercicio_not_found_route_params()
    {
        $this->actingAs($this->propietario);

        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(404);

        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1
        ]));
        $response->assertStatus(404);
    }
    public function test_eliminar_ejercicio_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->deleteJson(route("eliminar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);
        $response->assertExactJson([]);
        $this->assertSoftDeleted($this->ejercicio);
    }
}
