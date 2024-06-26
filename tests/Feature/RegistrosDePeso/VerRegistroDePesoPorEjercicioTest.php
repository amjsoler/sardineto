<?php

namespace Tests\Feature\RegistrosDePeso;

use App\Models\Ejercicio;
use App\Models\EjercicioUsuario;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerRegistroDePesoPorEjercicioTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;

    protected $gimnasio;
    protected $gimnasio2;

    protected $ejercicio;
    protected $ejercicio2;

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
    }

    public function test_ver_registro_de_peso_por_ejercicio_sin_autenticar()
    {
        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio",[
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(401);
    }

    public function test_ver_registro_de_peso_por_ejercicio_sin_verificar_cuenta()
    {
        $userSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);

        $this->actingAs($userSinVerificar);

        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(460);
    }

    public function test_ver_registro_de_peso_por_ejercicio_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        $this->actingAs($this->administrador);
        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio2->id
        ]));
        $response->assertStatus(403);
    }

    public function test_ver_registro_de_peso_por_ejercicio_not_found_route_param()
    {
        $this->actingAs($this->propietario);

        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio",
        [
            "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
            "ejercicio" => $this->ejercicio->id
        ]
        ));
        $response->assertStatus(404);

        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_ver_registro_de_peso_por_ejercicio_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $ejerusu1 = EjercicioUsuario::make([
            "unorm" => 50,
        ]);
        $ejerusu1->ejercicio = $this->ejercicio->id;
        $this->propietario->registrosPeso()->save($ejerusu1);

        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->first(fn (AssertableJson $json) => $json
                ->has("id")
                ->where("unorm", $ejerusu1->unorm)
                ->etc()
                ->where("ejercicio", $this->ejercicio->id)
            )
        );

        $response = $this->getJson(route("ver-registros-de-peso-por-ejercicio",
            [
                "gimnasio" => $this->gimnasio2->id,
                "ejercicio" => $this->ejercicio2->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }
}
