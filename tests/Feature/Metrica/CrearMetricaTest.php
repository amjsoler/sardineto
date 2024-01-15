<?php

namespace Tests\Feature\Metrica;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearMetricaTest extends TestCase
{
    public function test_crear_metrica_sin_autenticar()
    {
        $response = $this->postJson(route("crear-metrica"), []);
        $response->assertStatus(401);
    }

    public function test_crear_metrica_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $response = $this->postJson(route("crear-metrica"), []);
        $response->assertStatus(460);
    }

    public function test_crear_metrica_validation_fail()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $response = $this->postJson(route("crear-metrica"), [

        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("message")
            ->where("errors.peso.0", __("validation.metrica.peso.required"))
            ->where("errors.porcentaje_graso.0", __("validation.metrica.porcentaje_graso.required"))
        );

        $response = $this->postJson(route("crear-metrica"), [
            "peso" => 2.346,
            "porcentaje_graso" => 5.434
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.peso.0", __("validation.metrica.peso.decimal"))
            ->where("errors.porcentaje_graso.0", __("validation.metrica.porcentaje_graso.decimal"))
        );
    }

    public function test_crear_metrica_ok()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $this->assertEquals(0, $usuario->metricas()->count());
        $response = $this->postJson(route("crear-metrica"), [
            "peso" => 87.24,
            "porcentaje_graso" => 17
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("id")
            ->where("peso", 87.24)
            ->where("porcentaje_graso", 17)
            ->where("usuario", $usuario->id)
        );
        $this->assertEquals(1, $usuario->metricas()->count());
    }
}
