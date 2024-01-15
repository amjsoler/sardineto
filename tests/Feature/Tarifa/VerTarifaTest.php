<?php

namespace Tests\Feature\Tarifa;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerTarifaTest extends TestCase
{
    public function test_ver_tarifas_sin_autenticar()
    {
        $response = $this->getJson(route("ver-tarifas", 1));
        $response->assertStatus(401);
    }

    public function test_ver_tarifas_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario
        ]);

        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(460);
    }

    public function test_ver_tarifas_sin_autorizacion()
    {
        $usuarioNormal = User::factory()->create();
        $usuarioInvitado = User::factory()->create();
        $administrador = User::factory()->create();
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $this->actingAs($usuarioNormal);

        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(403);

        $this->actingAs($usuarioInvitado);
        $gimnasio->usuariosInvitados()->attach($usuarioInvitado);
        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->usuariosInvitados()->updateExistingPivot($usuarioInvitado->id, ["invitacion_aceptada" => 1]);
        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($administrador);
        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->administradores()->attach($administrador);
        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($propietario);
        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);
    }

    public function test_not_found_route_param()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $response = $this->getJson(route("ver-tarifas", Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_ver_tarifas_ok()
    {
        $usuario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);
        $response->assertExactJson([]);

        $tarifa1 = Tarifa::factory()->create([
            "gimnasio" => $gimnasio->id
        ]);

        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->first(fn (AssertableJson $json) =>
                $json->where("id", $tarifa1->id)
                    ->where("nombre", $tarifa1->nombre)
                    ->where("precio", (string)$tarifa1->precio)
                    ->where("creditos", $tarifa1->creditos)
                    ->where("gimnasio", $gimnasio->id)
            )
        );

        $tarifa2 = Tarifa::factory()->create([
            "gimnasio" => $gimnasio->id
        ]);

        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(2);

        $gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $tarifa3 = Tarifa::factory()->create([
            "gimnasio" => $gimnasio2->id
        ]);

        $response = $this->getJson(route("ver-tarifas", $gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }
}
