<?php

namespace Tests\Feature\Metrica;

use App\Models\Metrica;
use App\Models\User;
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
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $this->assertEquals(0, auth()->user()->metricas()->count());
        Metrica::factory()->create(["usuario" => auth()->user()]);
        $this->assertEquals(1, auth()->user()->metricas()->count());
    }
}
