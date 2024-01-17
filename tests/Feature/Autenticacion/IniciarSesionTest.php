<?php

namespace Tests\Feature\Autenticacion;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class IniciarSesionTest extends TestCase
{
    public function test_iniciar_sesion_sin_guest()
    {
        $usuarioRegistrado = User::factory()->create();

        $this->actingAs($usuarioRegistrado);

        $response = $this->postJson(route("iniciar-sesion"));
        $response->assertStatus(461);
    }

    public function test_iniciar_sesion_validation_fail()
    {
        $usuarioRegistrado = User::factory()->create();

        $response = $this->postJson(route("iniciar-sesion"),
        [

        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.email.0", __("validation.usuario.email.required"))
            ->where("errors.password.0", __("validation.usuario.password.required"))
        );



        $response = $this->postJson(route("iniciar-sesion"),
            [
                "email" => "estonoesuncorreovalido",
                "password" => "password"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.email.0", __("validation.usuario.email.email"))
        );



        $response = $this->postJson(route("iniciar-sesion"),
            [
                "email" => "correoinvent@correoinvent.com",
                "password" => "password"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.email.0", __("validation.usuario.email.exists"))
        );
    }

    public function test_iniciar_sesion_fail_contrasena()
    {
        $usuarioRegistrado = User::factory()->create();

        $response = $this->postJson(route("iniciar-sesion"),
            [
                "email" => $usuarioRegistrado->email,
                "password" => "Passinvent"
            ]);
        $response->assertStatus(462);
    }

    public function test_iniciar_sesion_ok()
    {
        $usuarioRegistrado = User::factory()->create();

        $response = $this->postJson(route("iniciar-sesion"),
            [
                "email" => $usuarioRegistrado->email,
                "password" => "password"
            ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("access_token")
            ->where("token_type", "Bearer")
        );
    }
}
