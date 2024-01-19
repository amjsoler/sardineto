<?php

namespace Tests\Feature\Suscripcion;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearSuscripcionComoAdminTest extends TestCase
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
    protected $tarifa2;

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
        $this->tarifa2 = Tarifa::factory()->create(["gimnasio" => $this->gimnasio2->id]);
    }

    public function test_crear_suscripcion_como_admin_sin_autenticacion()
    {
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(401);
    }

    public function test_crear_suscripcion_como_admin_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(460);
    }

    public function test_crear_suscripcion_como_admin_sin_autorizacion()
    {
        //Intento ver suscripciones como usuario normal
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(403);

        //ver suscripciones como admin OK
        $this->actingAs($this->administrador);
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(422);

        //ver suscripciones como propietario ok
        $this->actingAs($this->propietario);
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
            ]
        ));
        $response->assertStatus(422);
    }

    public function test_crear_suscripcion_como_admin_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_crear_suscripcion_como_admin_validation_fail()
    {
        $this->actingAs($this->propietario);

        //Tarifa:required, Usuario:required
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.tarifa.0", __("validation.suscripcion.tarifa.required"))
            ->where("errors.usuario.0", __("validation.suscripcion.usuario.exists"))
        );

        //Tarifa:exists para el gimnasio, Usuario:exists para el gimnasio
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ),
        [
            "tarifa" => $this->tarifa2->id,
            "usuario" => $this->usuarioInvitado->id
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.tarifa.0", __("validation.suscripcion.tarifa.exists"))
            ->where("errors.usuario.0", __("validation.suscripcion.usuario.exists"))
        );

        //Usuario con suscripción este mes
        $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ),
            [
                "tarifa" => $this->tarifa->id,
                "usuario" => $this->usuarioInvitadoAceptado->id
            ]);
        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ),
            [
                "tarifa" => $this->tarifa->id,
                "usuario" => $this->usuarioInvitadoAceptado->id
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.usuario.0", __("validation.suscripcion.usuarioQueSeSuscribe.ComprobarQueUserNoTieneSuscripcionEsteMes"))
        );
    }

    public function test_crear_suscripcion_como_admin_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("admin-crea-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id
            ]
        ),
            [
                "tarifa" => $this->tarifa->id,
                "usuario" => $this->usuarioInvitadoAceptado->id
            ]
        );
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("id")
            ->where("usuario", $this->usuarioInvitadoAceptado->id)
            ->where("gimnasio", $this->gimnasio->id)
            ->where("tarifa", $this->tarifa->id)
            ->where("pagada", null)
            ->where("creditos_restantes", $this->tarifa->creditos)
        );
    }
}
