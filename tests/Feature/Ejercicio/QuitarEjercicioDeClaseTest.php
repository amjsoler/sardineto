<?php

namespace Tests\Feature\Ejercicio;

use App\Models\Clase;
use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class QuitarEjercicioDeClaseTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;
    protected $gimnasio;
    protected $gimnasio2;
    protected $ejercicio;
    protected $ejercicio2;
    protected $clase;
    protected $clase2;

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
        $this->ejercicio2 = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio2]);

        $this->gimnasio->administradores()->attach($this->administrador);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio]);
        $this->clase->ejercicios()->attach($this->ejercicio, ["gimnasio" => $this->gimnasio->id, "detalles" => "detalles del ejercicio para esta clase"]);

        $this->clase2 = Clase::factory()->create(["gimnasio" => $this->gimnasio2]);
    }

    public function test_quitar_ejercicio_de_clase_sin_autenticacion()
    {
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(401);
    }

    public function test_quitar_ejercicio_de_clase_sin_verificar_cuenta()
    {
        $usuarioSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuarioSinVerificar);

        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(460);
    }
    public function test_quitar_ejercicio_de_clase_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        $this->clase->ejercicios()->attach($this->ejercicio, ["gimnasio" => $this->gimnasio->id, "detalles" => "detalles del ejercicio para esta clase"]);

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        //Comprobar ejercicio pertenece a gim
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio2->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio2->id
        ]));
        $response->assertStatus(403);

        //Comprobamos clase pertenece a gim
        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio2->id,
            "clase" => $this->clase2->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);
    }

    public function test_quitar_ejercicio_de_clase_not_found_route_params()
    {
        $this->actingAs($this->propietario);

        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(404);

        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => Clase::orderBy("id", "desc")->first()->id+1,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(404);

        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1
        ]));
        $response->assertStatus(404);
    }

    public function test_quitar_ejercicio_de_clase_ok()
    {
        $this->assertEquals(1, $this->clase->ejercicios()->count());

        $this->actingAs($this->propietario);

        $response = $this->getJson(route("desasociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where("id", $this->clase->id)
            ->etc()
            ->has("ejercicios", 0)
        );
    }
}
