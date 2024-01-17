<?php

namespace Tests\Feature\Autenticacion;

use App\Models\AccountVerifyToken;
use App\Models\User;
use App\Notifications\VerificarNuevaCuentaUsuario;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerificarCuentaTest extends TestCase
{
    public function test_verificar_cuenta_sin_autenticacion()
    {
        $response = $this->getJson(route("verificar-cuenta"));
        $response->assertStatus(401);
    }

    public function test_verificar_cuenta_con_cuenta_ya_verificada()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route("verificar-cuenta"));
        $response->assertStatus(463);
    }

    public function test_verificar_cuenta_ok()
    {
        Notification::fake();
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $tokensDeVerificacionGeneradosAntes = AccountVerifyToken::all()->count();

        Notification::assertCount(0);

        $response = $this->getJson(route("verificar-cuenta"));
        $response->assertStatus(200);

        $response = $this->getJson(route("verificar-cuenta"));
        $response->assertStatus(200);

        $tokensDeVerificacionGeneradosDespues = AccountVerifyToken::all()->count();
        $this->assertEquals(1, $tokensDeVerificacionGeneradosDespues - $tokensDeVerificacionGeneradosAntes);

        Notification::assertCount(2);
        Notification::assertSentTo($user, VerificarNuevaCuentaUsuario::class);
    }
}
