<?php

namespace Tests\Feature\Clase;

use App\Models\Clase;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerClasesTest extends TestCase
{
    public function test_ver_clases_sin_autenticacion()
    {
        $response = $this->getJson(route("ver-clases-de-gimnasio", 1));
        $response->assertStatus(401);
    }

    public function test_ver_clases_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);
        $gimnasio = Gimnasio::factory()->create(["propietario" => $user->id]);

        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(460);
    }

    public function test_ver_clases_sin_autorizacion()
    {
        $usuarioInvitado = User::factory()->create();
        $usuarioAdmin = User::factory()->create();
        $usuarioPropietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuarioPropietario
        ]);

        $gimnasio->administradores()->attach($usuarioAdmin);

        $this->actingAs($usuarioInvitado);
        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->usuariosInvitados()->attach($usuarioInvitado->id, ["invitacion_aceptada" => false]);
        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(403);

        $gimnasio->usuariosInvitados()->wherePivot("usuario", $usuarioInvitado->id)->update(["invitacion_aceptada" => true]);
        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($usuarioAdmin);
        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(200);

        $this->actingAs($usuarioPropietario);
        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(200);
    }

    public function test_ver_clases_sin_not_found_route_param()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->getJson(route("ver-clases-de-gimnasio", Gimnasio::orderBy("id", "desc")->first()->id+1));
        $response->assertStatus(404);
    }

    public function test_ver_clases_ok()
    {
        $usuarioPropietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create([
            "propietario" => $usuarioPropietario
        ]);
        $this->actingAs($usuarioPropietario);

        //Creo clases de prueba
        Clase::factory()->count(10)->create(["gimnasio" => $gimnasio->id]);

        $this->assertEquals(10, $gimnasio->clases()->count());

        $response = $this->getJson(route("ver-clases-de-gimnasio", $gimnasio->id));
        $response->assertStatus(200);
        $response->assertJsonCount(10);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has(10)
            ->first(fn (AssertableJson $json) => $json
                ->hasAll(["id", "nombre", "descripcion", "fechayhora", "plazas"])
                ->where("gimnasio", $gimnasio->id)
            )
        );
    }
}
