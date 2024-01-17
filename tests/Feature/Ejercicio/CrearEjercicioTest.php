<?php

namespace Tests\Feature\Ejercicio;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearEjercicioTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;
    protected $gimnasio;
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

        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitado);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoYAceptado, [
            "invitacion_aceptada" => true
        ]);
        $this->gimnasio->administradores()->attach($this->administrador);
    }

    public function test_crear_ejercicio_sin_autenticacion()
    {
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), []);
        $response->assertStatus(401);
    }

    public function test_crear_ejercicio_sin_verificar_cuenta()
    {
        $usuarioSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuarioSinVerificar);

        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), []);
        $response->assertStatus(460);
    }

    public function test_crear_ejercicio_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), []);
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), []);
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), []);
        $response->assertStatus(422);

        $this->actingAs($this->propietario);
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), []);
        $response->assertStatus(422);
    }

    public function test_crear_ejercicio_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->postJson(route("crear-ejercicio", Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_crear_ejercicio_validation_fail()
    {
        $this->actingAs($this->propietario);

        //Validamos nombre:required, demostración:url
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), [
            "demostracion" => "holabunastardes"
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.ejercicio.nombre.required"))
            ->where("errors.demostracion.0", __("validation.ejercicio.demostracion.url"))
        );

        //Validamos nombre:max y descripción:max
        $response = $this->postJson(route("crear-ejercicio", $this->gimnasio->id), [
            "nombre" => Str::random(151),
            "descripcion" => Str::random(501)
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.ejercicio.nombre.max"))
            ->where("errors.descripcion.0", __("validation.ejercicio.descripcion.max"))
        );
    }

    public function test_crear_ejercicio_ok()
    {
        $this->actingAs($this->propietario);

        $this->assertEquals(0, $this->gimnasio->ejercicios()->count());

        $response = $this->postJson(route("crear-ejercicio",
            $this->gimnasio->id), [
            "nombre" => "invent1",
            "descripcion" => "Hola invent de descripción",
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("id")
            ->where("nombre", "invent1")
            ->where("descripcion", "Hola invent de descripción")
            ->where("gimnasio", $this->gimnasio->id)
        );
    }
}
