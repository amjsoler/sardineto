<?php

namespace Tests\Feature\Metrica;

use App\Models\Metrica;
use App\Models\User;
use Tests\TestCase;

class EliminarMetricaTest extends TestCase
{
    public function test_eliminar_metrica_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-metrica", 1));
        $response->assertStatus(401);
    }

    public function test_eliminar_metrica_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $metrica = Metrica::factory()->create([
            "usuario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->deleteJson(route("eliminar-metrica", $metrica->id));
        $response->assertStatus(460);
    }

    public function test_eliminar_metrica_sin_authorization()
    {
        $usuario = User::factory()->create();
        $usuario2 = User::factory()->create();
        $metrica = Metrica::factory()->create([
            "usuario" => $usuario2->id
        ]);
        $this->actingAs($usuario);
        $response = $this->deleteJson(route("eliminar-metrica", $metrica->id));
        $response->assertStatus(403);
    }

    public function test_eliminar_metrica_not_found()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);
        $response = $this->deleteJson(route("eliminar-metrica", Metrica::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_eliminar_metrica_ok()
    {
        $usuario = User::factory()->create();
        $metrica = Metrica::factory()->create([
            "usuario" => $usuario->id
        ]);
        $this->actingAs($usuario);
        $this->assertEquals(1, $usuario->metricas()->count());
        $response = $this->deleteJson(route("eliminar-metrica", $metrica->id));
        $response->assertStatus(200);
        $this->assertEquals(0, $usuario->metricas()->count());
        $response->assertExactJson([]);
        $this->assertSoftDeleted($metrica);
    }
}
