<?php

namespace Tests\Feature\Autenticacion;

use App\Models\User;
use App\Notifications\EnviarSugerenciaAlAdministrador;
use Faker\Factory;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EnviarSugerenciaTest extends TestCase
{
    public function test_enviar_sugerencia_sin_autenticacion()
    {
        $response = $this->postJson(route("enviar-sugerencia"));
        $response->assertStatus(401);
    }

    public function test_enviar_sugerencia_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->postJson(route("enviar-sugerencia"));
        $response->assertStatus(460);
    }

    public function test_enviar_sugerencia_validation_fail()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //texto:required
        $response = $this->postJson(route("enviar-sugerencia"),
            [

            ]);
        $response->assertStatus(422);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->has("message")
            ->where("errors.texto.0", __("validation.enviarsugerencia.texto.required"))
        );

        //texto:max
        $response = $this->postJson(route("enviar-sugerencia"),
            [
                "texto" => Str::random(501)
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->has("message")
            ->where("errors.texto.0", __("validation.enviarsugerencia.texto.max"))
        );
    }

    public function test_enviar_sugerencia_ok()
    {
        $faker = Factory::create();

        Notification::fake();

        $user = User::firstOrCreate(["email" => env("ADMIN_AUTORIZADO")],
        ["name" => $faker->name, "password" => "password"]);

        $this->actingAs($user);

        $response = $this->postJson(route("enviar-sugerencia"),
            [
                "texto" => Str::random(100)
            ]);
        $response->assertStatus(200);

        Notification::assertCount(1);
        Notification::assertSentTo($user, EnviarSugerenciaAlAdministrador::class);
    }
}
