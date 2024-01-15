<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use App\Notifications\CorreoConfirmacionUsuarioInvitadoAGimnasio;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReenviarInvitacionTest extends TestCase
{
    public function test_reenviar_invitacion_a_gimnasio_sin_autenticacion()
    {
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => 1, "usuario" => 1])
        );
        $response->assertStatus(401);
    }

    public function test_reenviar_invitacion_a_gimnasio_sin_verificar_cuenta_usuario()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario->id])
        );
        $response->assertStatus(460);
    }

    public function test_reenviar_invitacion_a_gimnasio_no_authorization()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario2
        ]);

        //Comprobamos sin ser nada que da 403
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario1->id])
        );
        $response->assertStatus(403);

        //Ahora nos damos de alta como administrador para ver que ahora nos deja
        $gimnasio->administradores()->attach($usuario1->id);
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario1->id])
        );
        $response->assertStatus(422);

        //Siendo propietario debería dar 422 sin payload
        $this->actingAs($usuario2);
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario1->id])
        );
        $response->assertStatus(422);
    }

    public function test_reenviar_invitacion_a_gimnasio_validation_fail()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1
        ]);

        $this->assertEquals(0, $gimnasio->usuariosInvitados()->count());


        //Comprobamos los 404 de los modelos en ruta
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1, "usuario" => $usuario1->id])
        );
        $response->assertStatus(404);

        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => User::orderBy("id", "desc")->first()->id+1])
        );
        $response->assertStatus(404);

        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario1->id])
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("message")
            ->where("errors.usuarioId.0", __("validation.gimnasio.usuarioId.exists"))
        );

        //Ahora invito y acepto la invitación para ver si peta
        $gimnasio->usuariosInvitados()->attach($usuario1->id, ["invitacion_aceptada" => true]);
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario1->id])
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.usuarioId.0", __("validation.gimnasio.usuarioId.yaAceptado"))
        );
    }

    public function test_reenviar_invitacion_a_gimnasio_ok()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1
        ]);

        $gimnasio->usuariosInvitados()->attach($usuario2);

        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $usuario2->id])
        );
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    public function test_comprobar_que_notificacion_se_envia_al_usuario()
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->actingAs($user);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $user->id
        ]);

        $gimnasio->usuariosInvitados()->attach($user->id);
        $response = $this->getJson(
            route("reenviar-invitacion", ["gimnasio" => $gimnasio->id, "usuario" => $user->id])
        );

        Notification::assertSentTo($user, CorreoConfirmacionUsuarioInvitadoAGimnasio::class);
    }
}
