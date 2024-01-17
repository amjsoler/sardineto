<?php

namespace Tests\Feature\Articulo;

use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use App\Models\UsuarioCompraArticulo;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class HistorialComprasTest extends TestCase
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

    public function test_ver_historial_de_compras_sin_autenticar()
    {
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(401);
    }

    public function test_ver_historial_de_compras_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(460);
    }

    public function test_ver_historial_de_compras_sin_autorizacion()
    {
        //Usuario no invitado a gimnasio
        $this->actingAs($this->usuario_verificado);
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(403);

        //Usuario sin aceptar invitación a gimnasio
        $this->actingAs($this->usuarioInvitado);
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(200);

        //Administrador ok
        $this->actingAs($this->administrador);
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(200);

        //propietario ok
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(200);
    }

    public function test_ver_historial_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("articulos-historial-compras",
            ["gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1]));
        $response->assertStatus(404);
    }

    public function test_ver_historial_de_compras_ok()
    {
        $this->actingAs($this->usuarioInvitadoAceptado);
        $this->gimnasio2->usuariosInvitados()->attach($this->usuarioInvitadoAceptado, ["invitacion_aceptada" => true]);

        $art1Gimnasio1 = Articulo::factory()->create(["gimnasio" => $this->gimnasio->id]);
        $art2Gimnasio1 = Articulo::factory()->create(["gimnasio" => $this->gimnasio->id]);
        $art1Gimnasio2 = Articulo::factory()->create(["gimnasio" => $this->gimnasio2->id]);
        $art2Gimnasio2 = Articulo::factory()->create(["gimnasio" => $this->gimnasio2->id]);

        $this->usuarioInvitadoAceptado->historialDeCompras()->attach($art1Gimnasio1, ["gimnasio" => $this->gimnasio->id]);
        $this->usuarioInvitadoAceptado->historialDeCompras()->attach($art2Gimnasio1, ["gimnasio" => $this->gimnasio->id]);
        $this->usuarioInvitadoAceptado->historialDeCompras()->attach($art1Gimnasio2, ["gimnasio" => $this->gimnasio2->id]);
        $this->usuarioInvitadoAceptado->historialDeCompras()->attach($art2Gimnasio2, ["gimnasio" => $this->gimnasio2->id]);

        $response = $this->getJson(route("articulos-historial-compras", ["gimnasio" => $this->gimnasio->id]));
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->first(fn (AssertableJson $json) => $json
                ->has("id")
                ->where("nombre", $art1Gimnasio1->nombre)
                ->where("descripcion", $art1Gimnasio1->descripcion)
                ->where("stock", $art1Gimnasio1->stock)
                ->where("gimnasio", $this->gimnasio->id)
                ->has("pivot")
            )
        );
    }
}
