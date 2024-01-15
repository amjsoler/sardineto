<?php

namespace Tests\Feature\Articulo;

use App\Models\Articulo;
use App\Models\Gimnasio;
use App\Models\User;
use Tests\TestCase;

class EliminarArticuloTest extends TestCase
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

    public function test_eliminar_articulo_sin_autenticacion()
    {
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(401);
    }

    public function test_eliminar_articulo_sin_verificar_cuenta()
    {
        $this->actingAs($this->usuario_sin_verificar);
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(460);
    }

    public function test_eliminar_articulo_sin_autorizacion()
    {
        $this->actingAs($this->usuario_verificado);
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);

        $this->articulo = Articulo::factory()->create([
            "gimnasio" => $this->gimnasio->id
        ]);
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);

        $this->articulo = Articulo::factory()->create([
            "gimnasio" => $this->gimnasio->id
        ]);
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio2->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(403);
    }

    public function test_eliminar_articulo_ok()
    {
        $this->actingAs($this->propietario);
        $response = $this->deleteJson(route("eliminar-articulo", ["gimnasio" => $this->gimnasio->id, "articulo" => $this->articulo->id]));
        $response->assertStatus(200);
        $this->assertSoftDeleted($this->articulo);
        $response->assertExactJson([]);
    }
}
