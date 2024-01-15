<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use Tests\TestCase;

class QuitarAdministradorTest extends TestCase
{
    public function test_quitar_administrador_sin_authenticacion()
    {
        $usuario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario
        ]);

        $response = $this->deleteJson(route("quitar-administrador", [ "gimnasio" => $gimnasio->id, "usuario" => $usuario->id]));

        $response->assertStatus(401);
    }

    public function test_quitar_administrador_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario
        ]);

        $response = $this->deleteJson(route("quitar-administrador", [ "gimnasio" => $gimnasio->id, "usuario" => $usuario->id]));

        $response->assertStatus(460);
    }

    public function test_quitar_administrador_sin_autorizacion()
    {
        $usuario = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario2);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario
        ]);

        $gimnasio->administradores()->attach($usuario);

        $response = $this->deleteJson(route("quitar-administrador", [ "gimnasio" => $gimnasio->id, "usuario" => $usuario->id]));

        $response->assertStatus(403);
    }

    public function test_quitar_administrador_ok()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario1
        ]);

        $gimnasio->administradores()->attach($usuario2);

        $this->assertEquals(1, $gimnasio->administradores()->count());
        $response = $this->deleteJson(route("quitar-administrador", [ "gimnasio" => $gimnasio->id, "usuario" => $usuario2->id]));
        $this->assertEquals(0, $gimnasio->administradores()->count());

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }
}
