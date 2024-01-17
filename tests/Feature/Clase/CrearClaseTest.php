<?php

namespace Tests\Feature\Clase;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CrearClaseTest extends TestCase
{
    public function test_crear_clase_sin_autenticacion()
    {
        $response = $this->postJson(route("crear-clase", 1));
        $response->assertStatus(401);
    }

    public function test_crear_clase_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);
        $gimnasio = Gimnasio::factory()->create(["propietario" => $user->id]);

        $response = $this->postJson(route("crear-clase", $gimnasio->id));
        $response->assertStatus(460);
    }
    public function test_crear_clase_sin_autorizacion()
    {
        $usuarioInvitado = User::factory()->create();
        $usuarioAdmin = User::factory()->create();
        $usuarioPropietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuarioPropietario
        ]);

        $gimnasio->administradores()->attach($usuarioAdmin);

        $this->actingAs($usuarioInvitado);
        $response = $this->postJson(route("crear-clase", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->usuariosInvitados()->attach($usuarioInvitado->id, ["invitacion_aceptada" => false]);
        $response = $this->postJson(route("crear-clase", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuarioInvitado->id)->update(["invitacion_aceptada" => true]);
        $response = $this->postJson(route("crear-clase", $gimnasio->id));
        $response->assertStatus(403);

        $this->actingAs($usuarioAdmin);
        $response = $this->postJson(route("crear-clase", $gimnasio->id));
        $response->assertStatus(422);

        $this->actingAs($usuarioPropietario);
        $response = $this->postJson(route("crear-clase", $gimnasio->id));
        $response->assertStatus(422);
    }

    public function test_crear_clase_not_found_route_param()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson(route("crear-clase",
        Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }
    public function test_crear_clase_validation_fail()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create(["propietario" => $propietario->id]);
        $this->actingAs($propietario);

        //Nombre:required, descripcion:max, fechayhora:required, plazas:required
        $response = $this->postJson(route("crear-clase", $gimnasio->id),
        [
            "descripcion" => Str::random(5001),
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.clase.nombre.required"))
            ->where("errors.descripcion.0", __("validation.clase.descripcion.max"))
            ->where("errors.plazas.0", __("validation.clase.plazas.required"))
        );




        //Nombre:max, plazas:integer
        $response = $this->postJson(route("crear-clase", $gimnasio->id),
            [
                "nombre" => Str::random(151),
                "fechayhora" => "fechainvent",
                "plazas" => 2.1
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.nombre.0", __("validation.clase.nombre.max"))
            ->where("errors.fechayhora.0", __("validation.clase.fechayhora.date"))
            ->where("errors.plazas.0", __("validation.clase.plazas.integer"))
        );


        //plazas:integer
        $response = $this->postJson(route("crear-clase", $gimnasio->id),
            [
                "nombre" => "clasetest",
                "plazas" => 0
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.plazas.0", __("validation.clase.plazas.min"))
        );
    }

    public function test_crear_clase_ok()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create(["propietario" => $propietario->id]);
        $this->actingAs($propietario);
        $now = now()->toDateTimeLocalString();

        $response = $this->postJson(route("crear-clase", $gimnasio->id),
            [
                "nombre" => "clase de prueba",
                "fechayhora" => $now,
                "plazas" => 20
            ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("id")
            ->where("nombre", "clase de prueba")
            ->where("fechayhora", $now)
            ->where("plazas", 20)
            ->where("gimnasio", $gimnasio->id)
        );
    }
}
