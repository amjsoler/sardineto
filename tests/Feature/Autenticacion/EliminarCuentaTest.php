<?php

namespace Tests\Feature\Autenticacion;

use App\Models\User;
use Tests\TestCase;

class EliminarCuentaTest extends TestCase
{
    public function test_eliminar_cuenta_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-cuenta"));
        $response->assertStatus(401);
    }

    public function test_eliminar_cuenta_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->deleteJson(route("eliminar-cuenta"));
        $response->assertStatus(460);
    }

    public function test_eliminar_cuenta_ok()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson(route("eliminar-cuenta"));
        $response->assertStatus(200);
        $this->assertSoftDeleted($user);
    }
}
