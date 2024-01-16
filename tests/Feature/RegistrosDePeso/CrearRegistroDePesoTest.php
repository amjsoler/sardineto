<?php

namespace Tests\Feature\RegistrosDePeso;

use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearRegistroDePesoTest extends TestCase
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

    public function test_crear_registro_de_peso_sin_autenticar()
    {
        $response = $this->postJson(route("crear-registros-de-peso",
        [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(401);
    }

    public function test_crear_registro_de_peso_sin_verificar_cuenta()
    {
        $userSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);

        $this->actingAs($userSinVerificar);

        $response = $this->postJson(route("crear-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(460);
    }

    public function test_crear_registro_de_peso_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->postJson(route("crear-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->postJson(route("crear-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(422);

        $this->actingAs($this->administrador);
        $response = $this->postJson(route("crear-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(422);

        $this->actingAs($this->propietario);
        $response = $this->postJson(route("crear-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(422);

        $this->actingAs($this->propietario);
        $response = $this->postJson(route("crear-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio2->id
        ]));
        $response->assertStatus(403);
    }

    public function test_crear_registro_de_peso_not_found_route_param()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("crear-registros-de-peso",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "ejercicio" => $this->ejercicio->id
            ]
        ));
        $response->assertStatus(404);

        $response = $this->postJson(route("crear-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_crear_registro_de_peso_validation_fail()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("crear-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id
            ]
        ));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.unorm.0", __("validation.ejerciciousuario.unorm.required"))
        );

        $response = $this->postJson(route("crear-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id
            ]
        ),
            ["unorm" => 50.342]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.unorm.0", __("validation.ejerciciousuario.unorm.decimal"))
        );
    }

    public function test_crear_registro_de_peso_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("crear-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id
            ]
        ),
            ["unorm" => 50.34]
        );
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("id")
            ->where("unorm", 50.34)
            ->where("ejercicio", $this->ejercicio->id)
            ->where("usuario", $this->propietario->id)
        );
    }
}
