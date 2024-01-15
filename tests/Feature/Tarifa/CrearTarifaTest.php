<?php

namespace Tests\Feature\Tarifa;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearTarifaTest extends TestCase
{
    public function test_crear_tarifa_sin_autenticar()
    {
        $response = $this->postJson(route("crear-tarifas", 1));
        $response->assertStatus(401);
    }

    public function test_crear_tarifa_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario
        ]);

        $response = $this->postJson(route("crear-tarifas", $gimnasio->id));
        $response->assertStatus(460);
    }

    public function test_crear_tarifa_sin_autorizacion()
    {
        $administrador = User::factory()->create();
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $this->actingAs($administrador);

        $response = $this->postJson(route("crear-tarifas", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->administradores()->attach($administrador);
        $response = $this->postJson(route("crear-tarifas", $gimnasio->id));
        $response->assertStatus(403);

        $this->actingAs($propietario);
        $response = $this->postJson(route("crear-tarifas", $gimnasio->id));
        $response->assertStatus(200);
    }

    public function test_not_found_route_param()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);
        $response = $this->postJson(route("crear-tarifas", Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_crear_tarifa_validation_fail()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);
        $this->actingAs($propietario);

        $response = $this->postJson(route("crear-tarifas", $gimnasio->id), [

        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("message")
                ->where("errors.nombre.0", __("validation.tarifa.nombre.required"))
                ->where("errors.precio.0", __("validation.tarifa.precio.required"))
                ->where("errors.creditos.0", __("validation.tarifa.creditos.required"))
        );

        $response = $this->postJson(route("crear-tarifas", $gimnasio->id), [
            "nombre" => Str::random(151),
            "precio" => 2.342,
            "creditos" => 1.5
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.nombre.0", __("validation.tarifa.nombre.max"))
            ->where("errors.precio.0", __("validation.tarifa.precio.decimal"))
            ->where("errors.creditos.0", __("validation.tarifa.creditos.integer"))
        );

        $response = $this->postJson(route("crear-tarifas", $gimnasio->id), [
            "nombre" => "ok",
            "precio" => -3,
            "creditos" => -1
        ]);
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
        $this->actingAs($propietario);

        $response = $this->postJson(route("crear-tarifas", $gimnasio->id), [
            "nombre" => "OK",
            "precio" => 50,
            "creditos" => 10
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where("nombre", "OK")
            ->where("precio", 50)
            ->where("creditos", 10)
            ->where("gimnasio", $gimnasio->id)
            ->has("id")
        );
    }
}
