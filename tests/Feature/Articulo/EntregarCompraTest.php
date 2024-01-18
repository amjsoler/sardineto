<?php

namespace Tests\Feature\Articulo;

use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use App\Models\UsuarioCompraArticulo;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EntregarCompraTest extends TestCase
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

    protected $compra;

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

        $this->compra = UsuarioCompraArticulo::make();
        $this->compra->usuario = $this->propietario->id;
        $this->compra->articulo = $this->articulo->id;
        $this->compra->gimnasio = $this->gimnasio->id;
        $this->compra->save();
    }

    public function test_entregar_compra_sin_autenticar()
    {
        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(401);
    }

    public function test_entregar_compra_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);

        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(460);
    }

    public function test_entregar_compra_sin_autorizacion()
    {
        //Intentar entregar como usuario normal
        $this->actingAs($this->usuarioInvitadoAceptado);
        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(403);

        //entregar como admin OK
        $this->actingAs($this->administrador);
        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(200);

        //entregar como propietario ok
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(422); //<-- 422 porque la compra ya está entregada del test anterior. Lo que importa es ver que pasa el policy

        //entregar compra de otro gimnasio
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio2->id,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(403);
    }

    public function test_entregar_compra_not_found_route_params()
    {
        $this->actingAs($this->propietario);
        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
                "compra" => $this->compra->id
            ]
        ));
        $response->assertStatus(404);

        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio2->id,
                "compra" => UsuarioCompraArticulo::orderBy("id", "desc")->first()->id+1,
            ]
        ));
        $response->assertStatus(404);
    }

    public function test_entregar_compra_validation_fail()
    {
        $this->actingAs($this->propietario);

        $this->compra->entregada = now();
        $this->compra->save();

        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]));
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.compraId.0", __("validation.articulo.compra.ComprobarSiCompraYaEstaEntregada"))
        );
    }

    public function test_entregar_compra_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->getJson(route("entregar-compra",
            [
                "gimnasio" => $this->gimnasio->id,
                "compra" => $this->compra->id
            ]));
        $response->assertStatus(200);
        $response->assertExactJson([]);
        $this->assertNotNull($this->compra->refresh()->entregada);
    }
}
