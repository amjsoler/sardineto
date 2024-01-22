<?php

namespace Tests\Feature\Clase;

use App\Models\Clase;
use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DesapuntarmeDeClaseTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoAceptado;
    protected $administrador;
    protected $propietario;

    protected $gimnasio;
    protected $gimnasio2;
    protected $clase;

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
    }

    public function test_desapuntarse_de_clase_sin_autenticacion()
    {
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(401);
    }

    public function test_desapuntarse_de_clase_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(460);
    }

    public function test_desapuntarse_de_clase_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);

        $this->actingAs($this->administrador);
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio]);
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio2->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(403);
    }

    public function test_desapuntarse_de_clase_not_found_route_param()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(404);

        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => Clase::orderBy("id", "desc")->first()->id+1
            ]));
        $response->assertStatus(404);
    }

    public function test_desapuntarse_de_clase_validation_fail()
    {
        $this->actingAs($this->propietario);

        //Test usuario no apuntado a clase
        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.claseId.0", __("validation.usuarioApuntaClase.usuarioId.exists"))
        );

        //Test clase pasada ya
        $this->clase = Clase::factory()->create([
            "gimnasio" => $this->gimnasio,
            "fechayhora" => now()->subDay(),
            "plazas" => 1
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $this->gimnasio->id
        ]);
        $suscripcion = Suscripcion::factory()->create(["usuario" => $this->propietario->id, "tarifa" => $this->gimnasio->tarifas()->first()->id, "gimnasio" => $this->gimnasio->id, "creditos_restantes" => 5]);
        $this->clase->participantes()->attach($this->propietario, ["suscripcion" => $this->propietario->suscripciones()->first()->id]);

        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id,
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.fechayhora.0", __("validation.usuarioApuntaClase.fechayhora.after"))
        );
    }

    public function test_desapuntarse_de_clase_ok()
    {
        $this->actingAs($this->propietario);

        $this->clase = Clase::factory()->create([
            "gimnasio" => $this->gimnasio,
            "fechayhora" => now()->addDay(),
            "plazas" => 1
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $this->gimnasio->id
        ]);
        $suscripcion = Suscripcion::factory()->create(["usuario" => $this->propietario->id, "tarifa" => $this->gimnasio->tarifas()->first()->id, "gimnasio" => $this->gimnasio->id, "creditos_restantes" => 5]);
        $this->clase->participantes()->attach($this->propietario, ["suscripcion" => $this->propietario->suscripciones()->first()->id]);


        $response = $this->getJson(route("usuario-se-desapunta",
            [
                "gimnasio" => $this->gimnasio->id,
                "clase" => $this->clase->id
            ]));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $this->assertEquals(6, $suscripcion->refresh()->creditos_restantes);
    }
}
