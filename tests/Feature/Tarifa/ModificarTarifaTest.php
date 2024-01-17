<?php

namespace Tarifa;

use App\Models\Gimnasio;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ModificarTarifaTest extends TestCase
{
    public function test_editar_tarifa_sin_autenticar()
    {
        $response = $this->putJson(route("editar-tarifas",
            ["gimnasio" => 1, "tarifa" => 1]));
        $response->assertStatus(401);
    }

    public function test_editar_tarifa_sin_verificar_cuenta()
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

        $response = $this->putJson(route("editar-tarifas",
            ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(460);
    }

    public function test_editar_tarifa_sin_autorizacion()
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
        $response = $this->putJson(route("editar-tarifas",
            ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(403);

        $gimnasio->administradores()->attach($administrador);
        $response = $this->putJson(route("editar-tarifas",
            ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(200);

        $this->actingAs($propietario);
        $response = $this->putJson(route("editar-tarifas",
            ["gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(200);

        $gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);

        $response = $this->putJson(route("editar-tarifas",
            ["gimnasio" => $gimnasio2->id, "tarifa" => $tarifa->id]));
        $response->assertStatus(403);
    }

    public function test_editar_tarifa_not_found_route_param()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);

        $response = $this->putJson(route("editar-tarifas",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "tarifa" => $tarifa->id
            ]));
        $response->assertStatus(404);

        $response = $this->putJson(route("editar-tarifas",
            [
                "gimnasio" => $gimnasio->id,
                "tarifa" => Tarifa::orderBy("id", "desc")->first()->id+1
            ]));
        $response->assertStatus(404);
    }

    public function test_editar_tarifa_validation_fail()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);
        $this->actingAs($propietario);

        $response = $this->putJson(route("editar-tarifas",
            [
                "gimnasio" => $gimnasio->id, "tarifa" => $tarifa->id
            ]),
            [
                "nombre" => Str::random(151),
                "precio" => 50.243,
                "creditos" => 1.5
            ]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.nombre.0", __("validation.tarifa.nombre.max"))
            ->where("errors.precio.0", __("validation.tarifa.precio.decimal"))
            ->where("errors.creditos.0", __("validation.tarifa.creditos.integer"))
        );

        $response = $this->putJson(route("editar-tarifas",
            [
                "gimnasio" => $gimnasio->id,
                "tarifa" => $tarifa->id
            ]),
            [
                "precio" => -1,
                "creditos" => -1
            ]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.precio.0", __("validation.tarifa.precio.min"))
            ->where("errors.creditos.0", __("validation.tarifa.creditos.min"))
        );
    }

    public function test_crear_tarifa_ok()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $tarifa = Tarifa::factory()->create([
            "gimnasio" => $gimnasio
        ]);
        $this->actingAs($propietario);

        $response = $this->putJson(route("editar-tarifas",
            [
                "gimnasio" => $gimnasio->id,
                "tarifa" => $tarifa->id
            ]),
            [
                "nombre" => "EDIT",
                "precio" => 50,
                "creditos" => 10
            ]
        );
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->where("nombre", "EDIT")
            ->where("precio", 50)
            ->where("creditos", 10)
            ->where("gimnasio", $gimnasio->id)
            ->has("id")
        );
    }
}
