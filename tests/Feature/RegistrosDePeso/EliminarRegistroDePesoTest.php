<?php

namespace Tests\Feature\RegistrosDePeso;

use App\Models\Ejercicio;
use App\Models\EjercicioUsuario;
use App\Models\Gimnasio;
use App\Models\User;
use Tests\TestCase;

class EliminarRegistroDePesoTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;

    protected $gimnasio;
    protected $gimnasio2;

    protected $ejercicio;
    protected $ejercicio2;
    protected $ejercicioUsuarioInvitadoAceptao;
    protected $ejercicioUsuarioAdministrador;
    protected $ejercicioUsuario;


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

        $this->ejercicioUsuarioInvitadoAceptao = EjercicioUsuario::make(["unorm" => 50.25]);
        $this->ejercicioUsuarioInvitadoAceptao->ejercicio = $this->ejercicio->id;
        $this->ejercicioUsuarioInvitadoAceptao->usuario = $this->usuarioInvitadoYAceptado->id;
        $this->ejercicioUsuarioInvitadoAceptao->save();

        $this->ejercicioUsuarioAdministrador = EjercicioUsuario::make(["unorm" => 50.25]);
        $this->ejercicioUsuarioAdministrador->ejercicio = $this->ejercicio->id;
        $this->ejercicioUsuarioAdministrador->usuario = $this->administrador->id;
        $this->ejercicioUsuarioAdministrador->save();

        $this->ejercicioUsuario = EjercicioUsuario::make(["unorm" => 50.25]);
        $this->ejercicioUsuario->ejercicio = $this->ejercicio->id;
        $this->ejercicioUsuario->usuario = $this->propietario->id;
        $this->ejercicioUsuario->save();
    }

    public function test_eliminar_registro_de_peso_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-registros-de-peso",[
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(401);
    }
    public function test_eliminar_registro_de_peso_sin_verificar_cuenta()
    {
        $userSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);

        $this->actingAs($userSinVerificar);

        $response = $this->deleteJson(route("eliminar-registros-de-peso",[
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(460);
    }

    public function test_eliminar_registro_de_peso_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuarioInvitadoAceptao->id
        ]));
        $response->assertStatus(200);//<-403 porque no es su marca

        $this->actingAs($this->administrador);
        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuarioAdministrador->id
        ]));
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(200);

        $this->ejercicioUsuario = EjercicioUsuario::make(["unorm" => 50.25]);
        $this->ejercicioUsuario->ejercicio = $this->ejercicio->id;
        $this->ejercicioUsuario->usuario = $this->propietario->id;
        $this->ejercicioUsuario->save();

        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio2->id,
            "ejercicio" => $this->ejercicio->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(403);

        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio->id,
            "ejercicio" => $this->ejercicio2->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(403);

        $response = $this->deleteJson(route("eliminar-registros-de-peso", [
            "gimnasio" =>  $this->gimnasio2->id,
            "ejercicio" => $this->ejercicio2->id,
            "ejercicioUsuario" => $this->ejercicioUsuario->id
        ]));
        $response->assertStatus(403);
    }

    public function test_eliminar_registro_de_peso_not_found_rout_param()
    {
        $this->actingAs($this->propietario);

        $response = $this->deleteJson(route("eliminar-registros-de-peso",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "ejercicio" => $this->ejercicio->id,
                "ejercicioUsuario" => $this->ejercicioUsuario->id
            ]
        ));
        $response->assertStatus(404);

        $response = $this->deleteJson(route("eliminar-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1,
                "ejercicioUsuario" => $this->ejercicioUsuario->id
            ]
        ));
        $response->assertStatus(404);

        $response = $this->deleteJson(route("eliminar-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id,
                "ejercicioUsuario" => EjercicioUsuario::orderBy("id", "desc")->first()->id+1
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_eliminar_registro_de_peso_ok()
    {
        $this->actingAs($this->propietario);

        $this->assertEquals(1, auth()->user()->registrosPeso()->count());

        $response = $this->deleteJson(route("eliminar-registros-de-peso",
            [
                "gimnasio" => $this->gimnasio->id,
                "ejercicio" => $this->ejercicio->id,
                "ejercicioUsuario" => $this->ejercicioUsuario->id
            ]
        ));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $this->assertEquals(0, auth()->user()->registrosPeso()->count());
    }
}
