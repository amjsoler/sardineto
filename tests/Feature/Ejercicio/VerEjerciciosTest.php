<?php

namespace Tests\Feature\Ejercicio;

use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerEjerciciosTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;
    protected $gimnasio;
    protected $gimnasio2;

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
        $this->gimnasio->administradores()->attach($this->administrador);
    }

    public function test_ver_ejercicios_sin_autenticacion()
    {
        $response = $this->getJson(route("ver-ejercicios", 1));
        $response->assertStatus(401);
    }

    public function test_ver_ejercicios_sin_verificar_cuenta()
    {
        $usuarioSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuarioSinVerificar);

        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(460);
    }

    public function test_ver_ejercicios_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($this->administrador);
        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(200);


    }

    public function test_ver_ejercicios_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-ejercicios", Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_ver_ejercicios_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(0);

        $ejer1 = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio]);
        $ejer2 = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio]);
        $ejer3 = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio2]);

        $response = $this->getJson(route("ver-ejercicios", $this->gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->first(fn (AssertableJson $json) => $json
                ->has("id")
                ->where("nombre", $ejer1->nombre)
                ->where("descripcion", $ejer1->descripcion)
                ->where("demostracion", $ejer1->demostracion)
                ->where("gimnasio", $this->gimnasio->id)
            )
        );
    }
}
