<?php

namespace Tests\Feature\Clase;

use App\Models\Clase;
use App\Models\Gimnasio;
use App\Models\User;
use Tests\TestCase;

class EliminarClaseTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoAceptado;
    protected $administrador;
    protected $propietario;

    protected $gimnasio;
    protected $gimnasio2;
    protected $clase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarioInvitado = User::factory()->create();
        $this->usuarioInvitadoAceptado = User::factory()->create();
        $this->administrador = User::factory()->create();
        $this->propietario = User::factory()->create();

        $this->gimnasio = Gimnasio::factory()->create(["propietario" => $this->propietario->id]);
        $this->gimnasio2 = Gimnasio::factory()->create(["propietario" => $this->propietario->id]);

        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitado, ["invitacion_aceptada" => false]);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoAceptado);
        $this->gimnasio->administradores()->attach($this->administrador);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio->id]);
    }

    public function test_eliminar_clase_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-clase",
        [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id
        ]));
        $response->assertStatus(401);
    }

    public function test_eliminar_clase_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(460);
    }

    public function test_eliminar_clase_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio]);
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio]);
        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio2->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(403);
    }

    public function test_eliminar_clase_not_found_route_param()
    {
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(404);

        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => Clase::orderBy("id", "desc")->first()->id+1
            ]));
        $response->assertStatus(404);
    }

    public function test_eliminar_clase_ok()
    {
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-clase",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);
        $this->assertSoftDeleted($this->clase);
    }
}
