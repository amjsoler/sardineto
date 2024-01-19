<?php

namespace Tests\Feature\Suscripcion;

use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerSuscripcionesDeGimnasioTest extends TestCase
{
    protected $usuario_sin_verificar;
    protected $usuario_verificado;
    protected $usuarioInvitado;
    protected $usuarioInvitadoAceptado;
    protected $administrador;
    protected $propietario;

    protected $gimnasio;
    protected $gimnasio2;

    protected $tarifa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario_sin_verificar = User::factory()->create([
            "email_verified_at" => null
        ]);

        $this->usuario_verificado = User::factory()->create();
        $this->usuarioInvitado = User::factory()->create();
        $this->usuarioInvitadoAceptado = User::factory()->create();
        $this->administrador = User::factory()->create();
        $this->propietario = User::factory()->create();

        $this->gimnasio = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);
        $this->gimnasio->administradores()->attach($this->administrador);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitado);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoAceptado, ["invitacion_aceptada" => true]);
        $this->gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->tarifa = Tarifa::factory()->create(["gimnasio" => $this->gimnasio->id]);
    }

    public function test_ver_suscripciones_sin_autenticacion()
    {
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ));
        $response->assertStatus(401);
    }

    public function test_ver_suscripciones_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ));
        $response->assertStatus(460);
    }

    public function test_ver_suscripciones_sin_autorizacion()
    {
        //Intento ver suscripciones como usuario normal
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(403);

        //ver suscripciones como admin OK
        $this->actingAs($this->administrador);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(200);

        //ver suscripciones como propietario ok
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(200);
    }

    public function test_ver_suscripciones_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_ver_suscripciones_ok()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $suscripcion1 = Suscripcion::factory()->make(["tarifa" => $this->tarifa->id]);
        $suscripcion1->usuario = $this->propietario->id;
        $suscripcion1->creditos_restantes = $this->tarifa->creditos;
        $this->gimnasio->suscripciones()->save($suscripcion1);
        $response = $this->getJson(route("ver-suscripciones",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->first(fn (AssertableJson $json) => $json
                ->has("id")
                ->where("usuario", $this->propietario->id)
                ->where("tarifa", $this->tarifa->id)
                ->where("gimnasio", $this->gimnasio->id)
                ->where("pagada", null)
                ->where("creditos_restantes", $this->tarifa->creditos)
            )
        );

    }
}
