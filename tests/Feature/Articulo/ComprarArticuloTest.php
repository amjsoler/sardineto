<?php

namespace Tests\Feature\Articulo;

use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ComprarArticuloTest extends TestCase
{
    protected $usuario_sin_verificar;
    protected $usuario_verificado;
    protected $usuarioInvitado;
    protected $usuarioInvitadoAceptado;
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
        $this->usuarioInvitado = User::factory()->create();
        $this->usuarioInvitadoAceptado = User::factory()->create();
        $this->administrador = User::factory()->create();

        $this->propietario = User::factory()->create();

        $this->gimnasio = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);
        $this->gimnasio->administradores()->attach($this->administrador);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitado);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoAceptado, ["invitacion_aceptada" => true]);
        $this->gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->articulo = Articulo::factory()->create([
            "gimnasio" => $this->gimnasio
        ]);
    }

    public function test_comprar_articulo_sin_autenticacion()
    {
        $response = $this->getJson(route("comprar-articulo",
        [
            "gimnasio" => $this->gimnasio->id,
            "articulo" => $this->articulo->id
        ]));

        $response->assertStatus(401);
    }

    public function test_comprar_articulo_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->getJson(route("comprar-articulo",
            [
                "gimnasio" => $this->gimnasio->id,
                "articulo" => $this->articulo->id
            ]));

        $response->assertStatus(460);
    }

    public function test_comprar_articulo_sin_autorizacion()
    {
        //Usuario no invitado a gimnasio
        $this->actingAs($this->usuario_verificado);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(403);

        //Usuario sin aceptar invitación a gimnasio
        $this->actingAs($this->usuarioInvitado);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);

        //Administrador ok
        $this->actingAs($this->administrador);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);

        //propietario ok
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);

        //Miramos si el artículo pertenece al gimnasio
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio2->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(403);
    }

    public function test_comprar_articulo_not_found_route_param()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(
            route("comprar-articulo",
                [
                    "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                    "articulo" => $this->articulo->id
                ]
            )
        );
        $response->assertStatus(404);

        $response = $this->getJson(
            route("comprar-articulo",
                [
                    "gimnasio" => $this->gimnasio->id,
                    "articulo" => Articulo::orderBy("id", "desc")->first()->id+1
                ]
            )
        );
        $response->assertStatus(404);
    }

    public function test_comprar_articulo_validation_fail()
    {
        //¿Qué pasa si el stock es 0?
        $this->articulo->stock = 0;
        $this->articulo->save();

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.stockdisponible.0", __("validation.articulo.stockdisponible.min"))
        );
    }

    public function test_comprar_articulo_ok()
    {
        $stockAnterior = $this->articulo->stock;

        $this->actingAs($this->propietario);
        $response = $this->getJson(route("comprar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);
        $this->assertEquals($stockAnterior-1, $this->articulo->refresh()->stock);
    }
}
