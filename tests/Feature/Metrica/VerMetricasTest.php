<?php

namespace Tests\Feature\Metrica;

use App\Models\Metrica;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerMetricasTest extends TestCase
{
    public function test_ver_metricas_sin_autenticar()
    {
        $response = $this->getJson(route("ver-metricas"));
        $response->assertStatus(401);
    }

    public function test_ver_metricas_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $response = $this->getJson(route("ver-metricas"));
        $response->assertStatus(460);
    }

    public function test_ver_metricas_ok()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $this->assertEquals(0, auth()->user()->metricas()->count());
        $metrica = Metrica::factory()->create(["usuario" => auth()->user()]);
        $response = $this->getJson(route("ver-metricas"));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->first(fn(AssertableJson $json) => $json
                ->has("id")
                ->where("peso", $metrica->peso)
                ->where("porcentaje_graso", $metrica->porcentaje_graso)
                ->where("usuario", $metrica->usuario)
            )
        );
    }
}
