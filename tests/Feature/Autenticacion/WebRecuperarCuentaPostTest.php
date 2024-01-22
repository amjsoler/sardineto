<?php

namespace Tests\Feature\Autenticacion;

use App\Models\RecuperarCuentaToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebRecuperarCuentaPostTest extends TestCase
{
    function test_recuperar_cuenta_token_invent()
    {
        $response = $this->post(route("recuperarcuentapost"));
        $response->assertInvalid();
    }

    function test_recuperar_cuenta_token_ok()
    {
        $user = User::factory()->create();
        $recuperarCuentaToken = RecuperarCuentaToken::create([
            "usuario" => $user->id,
            "token" => Hash::make(now()),
            "valido_hasta" => now()->addDay()
        ]);
        $recuperarCuentaToken->save();

        $response = $this->post(route("recuperarcuentapost",
            [
                "token" => $recuperarCuentaToken->token,
                "password" => "password",
                "password_confirmation" => "password"
            ]));

        $response->assertSee(__("vistas.cuentaUsuario.recuperarcuentaresult.ok1"));
    }
}
