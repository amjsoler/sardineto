<?php

namespace Autenticacion;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GuardarAjustesCuentaUsuarioTest extends TestCase
{
    public function test_guardar_ajustes_cuenta_sin_autenticacion()
    {
        $response = $this->postJson(route("guardar-ajustes-cuenta"));
        $response->assertStatus(401);
    }

    public function test_guardar_ajustes_cuenta_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->postJson(route("guardar-ajustes-cuenta"));
        $response->assertStatus(460);
    }

    public function test_guardar_ajustes_cuenta_validation_fail()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //alertasporcorreo:required, alertaspornotificacion:required
        $response = $this->postJson(route("guardar-ajustes-cuenta"),
        [

        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.alertasporcorreo.0", __("validation.usuario.alertasporcorreo.required"))
            ->where("errors.alertaspornotificacion.0", __("validation.usuario.alertaspornotificacion.required"))
        );


        //alertasporcorreo:boolean, alertaspornotificacion:boolean
        $response = $this->postJson(route("guardar-ajustes-cuenta"),
            [
                "alertasporcorreo" => "estonoesunbooleano",
                "alertaspornotificacion" => "estonoesunbooleano"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.alertasporcorreo.0", __("validation.usuario.alertasporcorreo.boolean"))
            ->where("errors.alertaspornotificacion.0", __("validation.usuario.alertaspornotificacion.boolean"))
        );
    }

    public function test_guardar_ajustes_cuenta_ok()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route("guardar-ajustes-cuenta"),
            [
                "alertasporcorreo" => false,
                "alertaspornotificacion" => false,
            ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where("alertasporcorreo", false)
            ->where("alertaspornotificacion", false)
            ->where("name", $user->name)
            ->where("id", $user->id)
            ->where("email", $user->email)
        );
    }
}
