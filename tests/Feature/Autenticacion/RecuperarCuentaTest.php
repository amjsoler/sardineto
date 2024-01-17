<?php

namespace Tests\Feature\Autenticacion;

use App\Models\RecuperarCuentaToken;
use App\Models\User;
use App\Notifications\RecuperarCuenta;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RecuperarCuentaTest extends TestCase
{
    public function test_recuperar_cuenta_validation_fail()
    {
        //Correo required
        $response = $this->postJson(
            route("recuperar-cuenta"),
            [

            ]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.correo.0", __("validation.usuario.correo.required"))
        );


        //Correo válido
        $response = $this->postJson(
            route("recuperar-cuenta"),
            [
                "correo" => "estonoesuncorreovalido"
            ]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.correo.0", __("validation.usuario.correo.email"))
        );
    }

    public function test_recuperar_cuenta_correo_no_registrado()
    {
        Notification::fake();

        $tokensDeRecuperacionTotales = RecuperarCuentaToken::all()->count();

        $response = $this->postJson(
            route("recuperar-cuenta"),
            [
                "correo" => "correonoregistrado@correonoregistrado.com"
            ]
        );
        $response->assertStatus(200);

        $tokensDeRecuperacionTotalesDespues = RecuperarCuentaToken::all()->count();
        $this->assertEquals($tokensDeRecuperacionTotalesDespues, $tokensDeRecuperacionTotales);
        Notification::assertNothingSent();
    }

    public function test_recuperar_cuenta_correo_registrado()
    {
        $userRegistrado = User::factory()->create();

        Notification::fake();

        $tokensDeRecuperacionTotales = RecuperarCuentaToken::all()->count();

        $response = $this->postJson(
            route("recuperar-cuenta"),
            [
                "correo" => $userRegistrado->email
            ]
        );
        $response->assertStatus(200);

        $tokensDeRecuperacionTotalesDespues = RecuperarCuentaToken::all()->count();
        $this->assertEquals(1, $tokensDeRecuperacionTotalesDespues - $tokensDeRecuperacionTotales);
        Notification::assertCount(1);
        Notification::assertSentTo($userRegistrado, RecuperarCuenta::class);
    }

    public function test_recuperar_cuenta_comprobar_gestion_tokens_con_varias_peticiones()
    {
        $userRegistrado = User::factory()->create();

        Notification::fake();

        $tokensDeRecuperacionTotales = RecuperarCuentaToken::all()->count();

        $response = $this->postJson(
            route("recuperar-cuenta"),
            [
                "correo" => $userRegistrado->email
            ]
        );
        $response->assertStatus(200);

        //Otra petición
        $response = $this->postJson(
            route("recuperar-cuenta"),
            [
                "correo" => $userRegistrado->email
            ]
        );
        $response->assertStatus(200);

        $tokensDeRecuperacionTotalesDespues = RecuperarCuentaToken::all()->count();
        $this->assertEquals(1, $tokensDeRecuperacionTotalesDespues - $tokensDeRecuperacionTotales);
        Notification::assertCount(2);
        Notification::assertSentTo($userRegistrado, RecuperarCuenta::class);
    }
}
