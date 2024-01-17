<?php

namespace Tests\Feature\Articulo;

use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerArticulosTest extends TestCase
{
    public function test_ver_articulos_sin_autenticacion()
    {
        $response = $this->getJson(route("ver-articulos", 1));
        $response->assertStatus(401);
    }

    public function test_ver_articulos_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(460);
    }

    public function test_ver_articulos_sin_authorization()
    {
        $usuarioInvitado = User::factory()->create();
        $usuarioAdmin = User::factory()->create();
        $usuarioPropietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuarioPropietario
        ]);

        $this->actingAs($usuarioInvitado);
        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->usuariosInvitados()->attach($usuarioInvitado->id, ["invitacion_aceptada" => true]);
        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($usuarioAdmin);
        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->administradores()->attach($usuarioAdmin->id);
        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($usuarioPropietario);
        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(200);
    }

    public function test_ver_articulos_ok()
    {
        $usuarioPropietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuarioPropietario
        ]);

        $gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $usuarioPropietario
        ]);

        $this->actingAs($usuarioPropietario);

        $articulo = Articulo::factory()->create([
            "gimnasio" => $gimnasio->id
        ]);

        $articulo2 = Articulo::factory()->create([
            "gimnasio" => $gimnasio2->id
        ]);

        $response = $this->getJson(route("ver-articulos", $gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->first(fn (AssertableJson $json) => $json
                ->where("id", $articulo->id)
                ->where("nombre", $articulo->nombre)
                ->where("descripcion", $articulo->descripcion)
                ->where("stock", $articulo->stock)
                ->where("gimnasio", $gimnasio->id)
            )
        );
    }
}
