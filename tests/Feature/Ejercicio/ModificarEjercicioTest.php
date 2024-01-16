<?php

namespace Tests\Feature\Ejercicio;

use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ModificarEjercicioTest extends TestCase
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

    public function test_modificar_ejercicio_sin_autenticacion()
    {
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(401);
    }

    public function test_modificar_ejercicio_sin_verificar_cuenta()
    {
        $usuarioSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuarioSinVerificar);

        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(460);
    }

    public function test_modificar_ejercicio_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(200);

        //Comprobar ejercicio pertenece a gim
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio2->id,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(403);

}

    public function test_modificar_ejercicio_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
            "ejercicio" => $this->ejercicio->id
        ]), []);
        $response->assertStatus(404);

        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1
        ]), []);
        $response->assertStatus(404);
    }

    public function test_modificar_ejercicio_validation_fail()
    {
        $this->actingAs($this->propietario);

        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "nombre" => Str::random(151),
            "descripcion" => Str::random(501),
            "demostracion" => "urlinvalida"
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.ejercicio.nombre.max"))
            ->where("errors.descripcion.0", __("validation.ejercicio.descripcion.max"))
            ->where("errors.demostracion.0", __("validation.ejercicio.demostracion.url"))
        );
    }

    public function test_modificar_ejercicio_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->putJson(route("modificar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "nombre" => "Nombre edit",
            "descripcion" => "descEdit",
            "demostracion" => "https://www.edit.com"
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where("id", $this->ejercicio->id)
            ->where("nombre", "Nombre edit")
            ->where("descripcion", "descEdit")
            ->where("demostracion", "https://www.edit.com")
            ->where("gimnasio", $this->gimnasio->id)
        );
    }
}
