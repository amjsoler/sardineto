<?php

namespace Tests\Feature\Suscripcion;

use App\Models\Gimnasio;
use App\Models\Suscripcion;
use App\Models\Tarifa;
use App\Models\User;
use Tests\TestCase;

class EliminarSuscripcionTest extends TestCase
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

    protected $suscripcion;
    protected $suscripcion2;

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

        $this->suscripcion = Suscripcion::factory()->create(["usuario" => $this->propietario->id, "gimnasio" => $this->gimnasio->id, "tarifa" => $this->tarifa->id, "created_at" => now(), "creditos_restantes" => $this->tarifa->creditos]);
        $this->suscripcion2 = Suscripcion::factory()->create(["usuario" => $this->propietario->id, "gimnasio" => $this->gimnasio2->id, "tarifa" => $this->tarifa2->id, "created_at" => now(), "creditos_restantes" => $this->tarifa2->creditos]);
    }

    public function test_eliminar_suscripcion_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(401);
    }

    public function test_eliminar_suscripcion_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(460);
    }

    public function test_eliminar_suscripcion_sin_autorizacion()
    {
        //Intento editar suscripciones como usuario normal
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(403);

        //ver suscripciones como admin OK
        $this->actingAs($this->administrador);
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(200);

        //ver suscripciones como propietario ok
        $this->suscripcion = Suscripcion::factory()->create(["usuario" => $this->propietario->id, "gimnasio" => $this->gimnasio->id, "tarifa" => $this->tarifa->id, "created_at" => now(), "creditos_restantes" => $this->tarifa->creditos]);
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(200);

        //editar suscripciones que no pertenecen a gimnasio
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion2->id
            ]
        ));
        $response->assertStatus(403);
    }

    public function test_eliminar_suscripcion_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(404);

        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => Suscripcion::orderBy("id", "desc")->first()->id+1
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_eliminar_suscripcion_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->deleteJson(route("eliminar-suscripcion",
            [
                "gimnasio" => $this->gimnasio->id,
                "suscripcion" => $this->suscripcion->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertExactJson([]);
        $this->assertSoftDeleted($this->suscripcion);
    }
}
