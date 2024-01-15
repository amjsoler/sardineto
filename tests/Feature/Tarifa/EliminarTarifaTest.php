<?php

namespace Tests\Feature\Tarifa;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EliminarTarifaTest extends TestCase
{
    public function test_liminar_tarifa_sin_autenticar()
    {
        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => 1, "tarifa" => 1]));
        $response->assertStatus(401);
    }

    public function test_eliminar_tarifa_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);

        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(460);
    }

    public function test_eliminar_tarifa_sin_autorizacion()
    {
        $administrador = User::factory()->create();
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);
        $this->actingAs($administrador);

        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(403);

        $gimnasio->administradores()->attach($administrador);
        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(200);

        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);

        $this->actingAs($propietario);
        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(200);

        $gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);
        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio2->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(403);
    }

    public function test_not_found_route_param()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);

        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1, "tarifa" => $tarifa->id]));
        $response->assertStatus(404);

        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio->id, "tarifa" => Tarifa::orderBy("id", "desc")->first()->id+1]));
        $response->assertStatus(404);
    }

    public function test_eliminar_tarifa_ok()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);
        $this->actingAs($propietario);

        $response = $this->deleteJson(route("eliminar-tarifas", ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(200);
        $response->assertExactJson([]);
        $this->assertSoftDeleted($tarifa);
    }
}
