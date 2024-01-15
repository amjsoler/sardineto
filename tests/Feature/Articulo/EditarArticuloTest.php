<?php

namespace Tests\Feature\Articulo;

use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EditarArticuloTest extends TestCase
{
    protected $usuario_sin_verificar;
    protected $usuario_verificado;
    protected $administrador;
    protected $propietario;
    protected $gimnasio;
    protected $gimnasio2;
    protected $articulo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuario_sin_verificar = User::factory()->create([
            "email_verified_at" => null
        ]);

        $this->usuario_verificado = User::factory()->create();
        $this->administrador = User::factory()->create();

        $this->propietario = User::factory()->create();

        $this->gimnasio = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);
        $this->gimnasio->administradores()->attach($this->administrador);

        $this->gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->articulo = Articulo::factory()->create([
            "gimnasio" => $this->gimnasio
        ]);
    }

    public function test_editar_articulo_sin_autenticacion()
    {
        $response = $this->putJson(route("editar-articulo", ["gimnasio" => 1, "articulo" => 1]));
        $response->assertStatus(401);
    }

    public function test_editar_articulo_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->putJson(route("editar-articulo", ["gimnasio" => $this->gimnasio->id,
            "articulo" => $this->articulo->id]));
        $response->assertStatus(460);
    }

    public function test_editar_articulo_sin_autorizacion()
    {
        $this->actingAs($this->usuario_verificado);
        $response = $this->putJson(
            route("editar-articulo",
            [
                "gimnasio" => $this->gimnasio->id,
                "articulo" => $this->articulo->id
            ]
            )
        );
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->putJson(
            route("editar-articulo",
                [
                    "gimnasio" => $this->gimnasio->id,
                    "articulo" => $this->articulo->id
                ]
            )
        );
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->putJson(
            route("editar-articulo",
                [
                    "gimnasio" => $this->gimnasio->id,
                    "articulo" => $this->articulo->id
                ]
            )
        );
        $response->assertStatus(200);

        $this->actingAs($this->administrador);
        $response = $this->putJson(
            route("editar-articulo",
                [
                    "gimnasio" => $this->gimnasio2->id,
                    "articulo" => $this->articulo->id
                ]
            )
        );
        $response->assertStatus(403);
    }

    public function test_editar_articulo_validation_fail()
    {
        $this->actingAs($this->propietario);
        $response = $this->putJson(
            route("editar-articulo",
                [
                    "gimnasio" => $this->gimnasio->id,
                    "articulo" => $this->articulo->id
                ]
            ),
            [
                "nombre" => Str::random(151),
                "descripcion" => Str::random(5001),
                "stock" => 15.34
            ]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.articulo.nombre.max"))
            ->where("errors.descripcion.0", __("validation.articulo.descripcion.max"))
            ->where("errors.stock.0", __("validation.articulo.stock.integer"))
        );

        //Otro assert más
        $response = $this->putJson(
            route("editar-articulo",
                [
                    "gimnasio" => $this->gimnasio->id,
                    "articulo" => $this->articulo->id
                ]
            ),
            [
                "stock" => -3
            ]
        );
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.stock.0", __("validation.articulo.stock.min"))
        );
    }

    public function test_editar_articulo_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->putJson(
            route("editar-articulo",
                [
                    "gimnasio" => $this->gimnasio->id,
                    "articulo" => $this->articulo->id
                ]
            ),
            [
                "nombre" => "EDIT",
                "descripcion" => "EDIT",
                "stock" => 54
            ]
        );
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where("id", $this->articulo->id)
            ->where("nombre", "EDIT")
            ->where("descripcion", "EDIT")
            ->where("stock", 54)
            ->where("gimnasio", $this->gimnasio->id)

        );
    }
}
