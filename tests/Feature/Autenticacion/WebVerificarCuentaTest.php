<?php

namespace Tests\Feature\Autenticacion;

use App\Models\AccountVerifyToken;
use Faker\Factory;
use Tests\TestCase;

class WebVerificarCuentaTest extends TestCase
{
    function test_verificar_cuenta_token_inventado()
    {
        $response = $this->get(route("verificarcuentacontoken",
            [
                "token" => "invent"
            ]
        ));
        $response->assertOk();
        $response->assertSee(__("vistas.cuentaUsuario.verificarcuenta.ko"));
    }

    function test_verificar_cuenta_token_ok()
    {
        $faker = Factory::create();

        $response = $this->postJson(route("registrarse"),
            [
                "name" => "Jorge",
                "email" => $faker->unique()->email,
                "password" => "password",
                "password_confirmation" => "password"
            ]);
        $response->assertStatus(200);
        $tokenAceptacion = AccountVerifyToken::where("usuario", $response->json("id"))->first()->token;

        $response = $this->get(route("verificarcuentacontoken", ["token" => $tokenAceptacion]));
        $response->assertOk();
        $response->assertSee(__("vistas.cuentaUsuario.verificarcuenta.ok"));
    }
}
