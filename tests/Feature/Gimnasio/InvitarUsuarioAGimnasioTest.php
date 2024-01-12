<?php

namespace Gimnasio;

use App\Events\UsuarioInvitadoAGimnasio;
use App\Listeners\EnviarCorreoConfirmacionUsuarioInvitadoAGimnasio;
use App\Models\Gimnasio;
use App\Models\User;
use App\Notifications\CorreoConfirmacionUsuarioInvitadoAGimnasio;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class InvitarUsuarioAGimnasioTest extends TestCase
{
    public function test_invitar_a_gimnasio_sin_autenticacion()
    {
        $response = $this->postJson(route("invitar-usuario", 1));

        $response->assertStatus(401);
    }

    public function test_invitar_a_gimnasio_sin_verificar_cuenta_usuario()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->postJson(route("invitar-usuario", $gimnasio->id));

        $response->assertStatus(460);
    }

    public function test_invitar_a_gimnasio_no_authorization()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario2
        ]);

        //Comprobamos sin ser nada que da 403
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id));
        $response->assertStatus(403);

        //Ahora nos damos de alta como administrador para ver que ahora nos deja
        $gimnasio->administradores()->attach($usuario1->id);
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id));
        $response->assertStatus(422);

        //Siendo propietario debería dar 422 sin payload
        $this->actingAs($usuario2);
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id));
        $response->assertStatus(422);
    }

    public function test_invitar_a_gimnasio_validation_fail()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1
        ]);

        $this->assertEquals(0, $gimnasio->usuariosInvitados()->count());

        //Comprobamos el required del email
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
        [

        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("message")
            ->where("errors.email.0", __("validation.gimnasio.email.required"))
        );

        //Comprobamos que es un email
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
            [
                "email" => "superinventdeemail"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.email.0", __("validation.gimnasio.email.email"))
        );

        //Comprobamos que el email existe en la tabla users
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
            [
                "email" => "correoinvent@correoinvent.com"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.email.0", __("validation.gimnasio.email.exists"))
        );

        //Comprobamos que el user no esté invitado ya al gimnasio
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
            [
                "email" => $usuario2->email
            ]);
        $response->assertStatus(200);
        $this->assertEquals(1, $gimnasio->usuariosInvitados()->count());

        //Vovemos a invitarlo para ver que peta
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
            [
                "email" => $usuario2->email
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.email.0", __("validation.gimnasio.email.comprobarSiUsuarioYaEstaInvitadoAGimnasio"))
        );
    }

    public function test_invitar_usuario_ok()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1
        ]);

        //Comprobamos el required del email
        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
            [
                "email" => $usuario2->email
            ]);
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    public function test_dispatch_invitar_usuario_event()
    {
        Event::fake();

        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1
        ]);

        $response = $this->postJson(route("invitar-usuario", $gimnasio->id),
            [
                "email" => $usuario2->email
            ]);
        $response->assertStatus(200);

        Event::assertDispatched(UsuarioInvitadoAGimnasio::class);
    }

    public function test_comprobar_listener_escucha_evento()
    {
        Event::fake();

        Event::assertListening(
            UsuarioInvitadoAGimnasio::class,
            EnviarCorreoConfirmacionUsuarioInvitadoAGimnasio::class
        );
    }

    public function test_comprobar_que_notificacion_se_envia_al_usuario()
    {
        Notification::fake();

        $user = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $user->id
        ]);

        $event = new UsuarioInvitadoAGimnasio($user, $gimnasio, "tokeninvent");
        $listener = new EnviarCorreoConfirmacionUsuarioInvitadoAGimnasio();
        $listener->handle($event);

        Notification::assertSentTo($user, CorreoConfirmacionUsuarioInvitadoAGimnasio::class);
    }
}
