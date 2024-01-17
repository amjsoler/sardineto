<?php

namespace Tests\Feature\Autenticacion;

use App\Models\User;
use App\Notifications\VerificarNuevaCuentaUsuario;
use Faker\Factory;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegistroTest extends TestCase
{
    public function test_registrarse_sin_guest()
    {
        $usuarioRegistrado = User::factory()->create();

        $this->actingAs($usuarioRegistrado);

        $response = $this->postJson(route("registrarse"));
        $response->assertStatus(461);
    }

    public function test_registrarse_validation_fail()
    {
        //Comprobamos:
        // Name.required, email.required, password.required
        $response = $this->postJson(route("registrarse"),
            [

            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.name.0", __("validation.usuario.name.required"))
            ->where("errors.email.0", __("validation.usuario.email.required"))
            ->where("errors.password.0", __("validation.usuario.password.required"))
        );


        //Comprobamos:
        // Name.max, email.email, password.confirmed
        $response = $this->postJson(route("registrarse"),
            [
                "name" => Str::random(101),
                "email" => "esto no es un email válido",
                "password" => "password"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.name.0", __("validation.usuario.name.max"))
            ->where("errors.email.0", __("validation.usuario.email.email"))
            ->where("errors.password.0", __("validation.usuario.password.confirmed"))
        );



        //Comprobamos:
        //email.unique
        $response = $this->postJson(route("registrarse"),
            [
                "name" => Str::random(101),
                "email" => "esto no es un email válido",
                "password" => "password"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.email.0", __("validation.usuario.email.email"))
        );
    }

    public function test_registrarse_ok()
    {
        $faker = Factory::create();

        $response = $this->postJson(route("registrarse"),
            [
                "name" => $faker->name,
                "email" => $faker->unique()->email,
                "password" => "password",
                "password_confirmation" => "password"
            ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("access_token")
            ->where("token_type", "Bearer")
            ->has("name")
            ->has("id")
            ->has("email")
        );
    }

    public function test_registrarse_comprobar_mail_mandado()
    {
        $faker = Factory::create();

        Notification::fake();
        Notification::assertNothingSent();

        $response = $this->postJson(route("registrarse"),
            [
                "name" => $faker->name,
                "email" => $faker->unique()->email,
                "password" => "password",
                "password_confirmation" => "password"
            ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("access_token")
            ->where("token_type", "Bearer")
            ->has("name")
            ->has("id")
            ->has("email")
        );

        Notification::assertCount(1);
        Notification::assertSentTo(User::find($response->original->id), VerificarNuevaCuentaUsuario::class);
    }
}
