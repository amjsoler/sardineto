<?php

namespace Tests\Feature\Articulo;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearArticuloTest extends TestCase
{
    public function test_crear_articulo_sin_autenticacion()
    {
        $usuario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->postJson(route("crear-articulo", $gimnasio->id));
        $response->assertStatus(401);
    }

    public function test_crear_articulo_sin_verificar_cuenta()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->postJson(route("crear-articulo", $gimnasio->id));
        $response->assertStatus(460);
    }

    public function test_crear_articulo_sin_autorizacion()
    {
        $administrador = User::factory()->create();
        $propietario = User::factory()->create();

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $propietario
        ]);

        $this->actingAs($administrador);
        $response = $this->postJson(route("crear-articulo", $gimnasio->id));
        $response->assertStatus(403);
        $gimnasio->administradores()->attach($administrador);
        $response = $this->postJson(route("crear-articulo", $gimnasio->id));
        $response->assertStatus(422);

        $this->actingAs($propietario);
        $response = $this->postJson(route("crear-articulo", $gimnasio->id));
        $response->assertStatus(422);
    }

    public function test_crear_articulo_gimnasio_not_found()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->postJson(route("crear-articulo", Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_crear_articulo_validation_fail()
    {
        $usuario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->postJson(route("crear-articulo", $gimnasio->id), [
            "descripcion" => Str::random(5001),
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.articulo.nombre.required"))
            ->where("errors.descripcion.0", __("validation.articulo.descripcion.max"))
            ->where("errors.stock.0", __("validation.articulo.stock.required"))
        );

        $response = $this->postJson(route("crear-articulo", $gimnasio->id), [
            "nombre" => Str::random(151),
            "stock" => 12.2
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.articulo.nombre.max"))
            ->where("errors.stock.0", __("validation.articulo.stock.integer"))
        );

        $response = $this->postJson(route("crear-articulo", $gimnasio->id), [
            "stock" => -3
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.stock.0", __("validation.articulo.stock.min"))
        );
    }

    public function test_crear_articulo_ok()
    {
        $usuario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);
        $this->actingAs($usuario);

        $response = $this->postJson(route("crear-articulo", $gimnasio->id), [
            "nombre" => "Superarticulo test",
            "descripcion" => Str::random(300),
            "stock" => 15
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("id")
            ->where("nombre", "Superarticulo test")
            ->has("descripcion")
            ->where("stock", 15)
            ->where("gimnasio", $gimnasio->id)
        );
    }
}
