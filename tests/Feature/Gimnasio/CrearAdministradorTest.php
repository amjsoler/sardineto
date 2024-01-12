<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearAdministradorTest extends TestCase
{
    public function test_crear_administrador_gimnasio_sin_autenticacion()
    {
        $response = $this->getJson(route("crear-administrador", ["gimnasio" => 1, "usuario" => 1]));
        $response->assertStatus(401);
    }

    public function test_crear_administrador_gimnasio_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->getJson(route("crear-administrador", ["gimnasio" => $gimnasio->id, "usuario" => $usuario->id]));
        $response->assertStatus(460);
    }

    public function test_crear_administrador_gimnasio_sin_autorizacion()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario2);

        //El usuario sin ser nada, no puede dar de alta administradores
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1->id
        ]);

        $response = $this->getJson(route("crear-administrador",
            ["gimnasio" => $gimnasio->id, "usuario" => $usuario2->id]));
        $response->assertStatus(403);

        //Un administrador tampoco puede dar de alta administradores
        $gimnasio->administradores()->attach($usuario2);
        $response = $this->getJson(route("crear-administrador",
            ["gimnasio" => $gimnasio->id, "usuario" => $usuario2->id]));
        $response->assertStatus(403);

        //Solo el propietario puede dar de alta admins
        $this->actingAs($usuario1);
        $usuario3 = User::factory()->create();

        $response = $this->getJson(route("crear-administrador",
            ["gimnasio" => $gimnasio->id, "usuario" => $usuario3->id]));
        $response->assertStatus(200);
    }

    public function test_crear_administrador_validation_fail()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        //El usuario sin ser nada, no puede dar de alta administradores
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1->id
        ]);

        $gimnasio->administradores()->attach($usuario2);

        //No puedo añadir un usuario a admins si ya lo está
        $response = $this->getJson(route("crear-administrador",
            ["gimnasio" => $gimnasio->id, "usuario" => $usuario2->id]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("message")
            ->where("errors.gimnasioId.0", __("validation.gimnasio.gimnasioId.unique"))
        );
    }

    public function test_crear_administrador_ok()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        //El usuario sin ser nada, no puede dar de alta administradores
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1->id
        ]);

        //No puedo añadir un usuario a admins si ya lo está
        $response = $this->getJson(route("crear-administrador",
            ["gimnasio" => $gimnasio->id, "usuario" => $usuario2->id]));
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }
}
