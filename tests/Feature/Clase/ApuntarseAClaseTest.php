<?php

namespace Tests\Feature\Clase;

use App\Enums\TiposTarifa;
use App\Models\Clase;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApuntarseAClaseTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoAceptado;
    protected $administrador;
    protected $propietario;

    protected $gimnasio;
    protected $gimnasio2;
    protected $clase;

    protected $tarifa;

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
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoAceptado, ["invitacion_aceptada" => true]);
        $this->gimnasio->administradores()->attach($this->administrador);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio->id]);
        $this->clase2 = Clase::factory()->create(["gimnasio" => $this->gimnasio2->id]);

        $this->tarifa = Tarifa::factory()->create(["gimnasio" => $this->gimnasio->id, "creditos" => 20]);
        $this->tarifa2 = Tarifa::factory()->create(["gimnasio" => $this->gimnasio2->id, "creditos" => 20]);
    }

    public function test_apuntarse_a_clase_sin_autenticacion()
    {
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(401);
    }

    public function test_apuntarse_a_clase_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(460);
    }

    public function test_apuntarse_a_clase_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);

        $this->actingAs($this->administrador);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio]);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio2->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(403);
    }

    public function test_apuntarse_a_clase_not_found_route_param()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(404);

        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => Clase::orderBy("id", "desc")->first()->id+1
            ]));
        $response->assertStatus(404);
    }

    public function test_apuntarse_a_clase_validation_fail()
    {
        $this->actingAs($this->administrador);

        //Clase con 1 plaza
        $this->clase = Clase::factory()->create(
            [
                "gimnasio" => $this->gimnasio->id,
                "plazas" => 1
            ]);

        //Test usuarioId.unique
        $suscripcion = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now(),
                "creditos_restantes" => $this->tarifa->creditos
            ]
        );
        $suscripcion->usuario = $this->administrador->id;
        $this->gimnasio->suscripciones()->save($suscripcion);

        //Suscripción 2
        $suscripcion2 = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa2->id,
                "created_at" => now(),
                "creditos_restantes" => $this->tarifa2->creditos
            ]
        );
        $suscripcion2->usuario = $this->administrador->id;
        $this->gimnasio2->suscripciones()->save($suscripcion2);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);

        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.usuarioId.0", __("validation.usuarioApuntaClase.unique"))
        );

        $this->gimnasio2->usuariosInvitados()->attach($this->administrador, ["invitacion_aceptada" => true]);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio2->id,
                "clase" => $this->clase2->id
            ]));
        $response->assertStatus(200);

        //Test clase:quedanplazas
        $this->actingAs($this->propietario);
        $suscripcionProp = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now(),
                "creditos_restantes" => $this->tarifa->creditos
            ]
        );
        $suscripcionProp->usuario = $this->propietario->id;
        $this->gimnasio->suscripciones()->save($suscripcionProp);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.claseId.0", __("validation.clase.claseId.ComprobarSiQuedanPlazasEnLaClase"))
        );

        //Clase con más plazas
        $this->clase = Clase::factory()->create(
            [
                "gimnasio" => $this->gimnasio->id,
                "plazas" => 10
            ]);

        Suscripcion::where("usuario", $this->propietario->id)->delete();

        //Test usuarioId: Está suscrito al gimnasio?
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.usuarioId.0", __("validation.clase.usuarioId.ComprobarSiUserReuneRequisitosParaApuntarseAClase"))
        );

        //Ahora suscribimos al propietario con una tarifa de suscripción en otro més diferente al actual: 422
        $suscripcion = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now()->subRealMonth(),
                "creditos_restantes" => $this->tarifa->creditos
            ]
        );
        $suscripcion->usuario = $this->propietario->id;
        $this->gimnasio->suscripciones()->save($suscripcion);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.usuarioId.0", __("validation.clase.usuarioId.ComprobarSiUserReuneRequisitosParaApuntarseAClase"))
        );

        //Ahora lo suscribimos con una tarifa tipo Abono en otro mes y sin créditos restantes: 422
        $suscripcion = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now()->subRealMonth(),
                "creditos_restantes" => 2
            ]
        );
        $suscripcion->usuario = $this->propietario->id;
        $this->gimnasio->suscripciones()->save($suscripcion);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.usuarioId.0", __("validation.clase.usuarioId.ComprobarSiUserReuneRequisitosParaApuntarseAClase"))
        );

        //Lo mismo que antes pero con créditos: 200
        $suscripcion = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now(),
                "creditos_restantes" => 1
            ]
        );
        $suscripcion->usuario = $this->propietario->id;
        $this->gimnasio->suscripciones()->save($suscripcion);
        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);
    }

    public function test_apuntarse_a_clase_ok()
    {
        //Usuario se apunta a clase con suscripción activa
        $this->actingAs($this->propietario);
        $suscripcion = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now(),
                "creditos_restantes" => 1
            ]
        );
        $suscripcion->usuario = $this->propietario->id;
        $this->gimnasio->suscripciones()->save($suscripcion);

        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $creditosRestantesDespues = $suscripcion->refresh()->creditos_restantes;
        $this->assertEquals(0, $creditosRestantesDespues);
dd($this->propietario->refresh()->clasesEnLasQueParticipa()->withPivot("suscripcion"));
        $this->assertEquals($suscripcion->id, $this->propietario->refresh()->clasesEnLasQueParticipa()->first()->suscripcion);


        //Usuario se apunta a clase con abono y suscripción activa
        $this->actingAs($this->propietario);
        $suscripcion = Suscripcion::factory()->make(
            [
                "pagada" => now(),
                "tarifa" => $this->tarifa->id,
                "created_at" => now()->subYear(),
                "tipo" => TiposTarifa::ABONO,
                "creditos_restantes" => 1
            ]
        );
        $suscripcion->usuario = $this->propietario->id;
        $this->gimnasio->suscripciones()->save($suscripcion);

        $this->propietario->clasesEnLasQueParticipa()->delete();

        $response = $this->getJson(route("usuario-se-apunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $creditosRestantesDespues = $suscripcion->creditos_restantes;
        $this->assertEquals(0, $creditosRestantesDespues);
        $this->assertEquals($suscripcion->id, $this->propietario->clasesEnLasQueParticipa()->first()->suscripcion);
    }
}
