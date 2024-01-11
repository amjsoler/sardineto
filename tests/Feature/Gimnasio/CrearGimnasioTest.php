<?php

namespace Tests\Feature\Gimnasio;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearGimnasioTest extends TestCase
{
    public function test_crear_gimnasio_sin_autenticacion()
    {
        $response = $this->post(route("crear-gimnasio"),
            [],
            [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]);

        $response->assertStatus(401);
    }

    public function test_crear_gimnasio_sin_verificar_cuenta_usuario()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $response = $this->post(route("crear-gimnasio"),
            [],
            [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]);

        $response->assertStatus(460);
    }

    public function test_crear_gimnasio_validation_ko()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        //Validamos el nombre required
        $response = $this->postJson(route("crear-gimnasio"),
        [
            "descripcion" => Str::random(5001),
            "direccion" => Str::random(201)
        ]);

        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("message")
                ->where("errors.nombre.0", __("validation.gimnasio.nombre.required"))
                ->where("errors.descripcion.0", __("validation.gimnasio.descripcion.max"))
                ->where("errors.direccion.0", __("validation.gimnasio.direccion.max"))
        );
    }

    public function test_crear_gimnasio_ok()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        //Validamos el nombre required
        $response = $this->postJson(route("crear-gimnasio"),
            [
                "nombre" => "prueba"
            ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where("nombre", "prueba")
            ->has("id")
            ->where("descripcion", null)
            ->where("logo", null)
            ->where("direccion", null)
        );
    }
}
