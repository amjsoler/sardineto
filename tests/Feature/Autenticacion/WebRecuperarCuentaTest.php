<?php

namespace Tests\Feature\Autenticacion;

use App\Models\RecuperarCuentaToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebRecuperarCuentaTest extends TestCase
{
    function test_recuperar_cuenta_token_invent()
    {
        $response = $this->get(route("recuperarcuentaget",
        [
            "token" => "invent"
        ]));

        $response->assertSee(__("vistas.cuentaUsuario.recuperarcuenta.ko"));
    }

    function test_recuperar_cuenta_token_invalido()
    {
        $user = User::factory()->create();
        $recuperarCuentaToken = RecuperarCuentaToken::create([
            "usuario" => $user->id,
            "token" => Hash::make(now()),
            "valido_hasta" => now()->subDay()
        ]);
        $recuperarCuentaToken->save();

        $response = $this->get(route("recuperarcuentaget",
            [
                "token" => $recuperarCuentaToken->token
            ]));

        $response->assertSee(__("vistas.cuentaUsuario.recuperarcuenta.ko"));
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

        $response = $this->get(route("recuperarcuentaget",
            [
                "token" => $recuperarCuentaToken->token
            ]));

        $response->assertSee(__("vistas.cuentaUsuario.recuperarcuenta.nuevacontrasena"));
    }
}
