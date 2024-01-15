<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ModificarGimnasioTest extends TestCase
{
    use RefreshDatabase;

    public function test_editar_gimnasio_sin_autenticacion()
    {
        $response = $this->putJson(route("editar-gimnasio", 1),
            []);

        $response->assertStatus(401);
    }

    public function test_editar_gimnasio_sin_verificar_cuenta_usuario()
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->putJson(route("editar-gimnasio", $gimnasio->id),
            []);

        $response->assertStatus(460);
    }

    public function test_editar_gimnasio_no_authorization()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();
        $this->actingAs($usuario1);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario2
        ]);

        //Comprobamos sin ser nada que da 403
        $response = $this->putJson(route("editar-gimnasio", $gimnasio->id), []);
        $response->assertStatus(403);

        //Ahora nos damos de alta como administrador para comprobar que cambia
        $gimnasio->administradores()->attach($usuario1->id);
        $response = $this->putJson(route("editar-gimnasio", $gimnasio->id), []);
        $response->assertStatus(200);

        //Siendo propietario debería dar 422 sin payload
        $this->actingAs($usuario2);
        $response = $this->putJson(route("editar-gimnasio", $gimnasio->id), []);
        $response->assertStatus(200);
    }

    public function test_ver_gimnasio_que_no_existe()
    {
        $usuario1 = User::factory()->create();
        $this->actingAs($usuario1);

        $response = $this->putJson(route("editar-gimnasio", 999));
        $response->assertStatus(404);
    }

    public function test_editar_gimnasio_validation_ko()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        //Validamos nombre:max, descripción:max y dirección:max
        $response = $this->putJson(route("editar-gimnasio", $gimnasio->id),
            [
                "nombre" => Str::random(151),
                "descripcion" => Str::random(5001),
                "direccion" => Str::random(201)
            ]);

        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has("message")
            ->where("errors.nombre.0", __("validation.gimnasio.nombre.max"))
            ->where("errors.descripcion.0", __("validation.gimnasio.descripcion.max"))
            ->where("errors.direccion.0", __("validation.gimnasio.direccion.max"))
        );
    }

    public function test_editar_gimnasio_ok()
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuario->id
        ]);

        $response = $this->putJson(route("editar-gimnasio", $gimnasio->id),
            [
                "nombre" => "editado",
                "descripcion" => "editado",
                "direccion" => "editado"
            ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->where("id", $gimnasio->id)
            ->where("nombre", "editado")
            ->where("descripcion", "editado")
            ->has("logo")
            ->where("direccion", "editado")
        );
    }
}
