<?php

namespace Tests\Feature\Autenticacion;


use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CambiarContrasenaTest extends TestCase
{
    public function test_cambiar_contrasena_sin_autenticacion()
    {
        $response = $this->postJson(route("cambiar-contrasena"));
        $response->assertStatus(401);
    }

    public function test_cambiar_contrasena_sin_verificar_cuenta()
    {
        $user = User::factory()->create(["email_verified_at" => null]);
        $this->actingAs($user);

        $response = $this->postJson(route("cambiar-contrasena"));
        $response->assertStatus(460);
    }

    public function test_cambiar_contrasena_validation_fail()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //Contraseña actual required, contraseña nueva required
        $response = $this->postJson(route("cambiar-contrasena"),
        [

        ]);
        $response->assertStatus(422);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->has("message")
            ->where("errors.contrasenaActual.0", __("validation.usuario.contrasenaActual.required"))
            ->where("errors.nuevaContrasena.0", __("validation.usuario.nuevaContrasena.required"))
        );

        //Contraseña actual correcta, contraseña nueva confirmed
        $response = $this->postJson(route("cambiar-contrasena"),
            [
                "contrasenaActual" => "contraseñainventquenoeslacorrecta",
                "nuevaContrasena" => "password"
            ]);
        $response->assertStatus(422);
        $response->assertJson(fn(AssertableJson $json) => $json
            ->has("message")
            ->where("errors.contrasenaActual.0", __("validation.usuario.contrasenaActual.ContrasenaActualCorrectaRule"))
            ->where("errors.nuevaContrasena.0", __("validation.usuario.nuevaContrasena.confirmed"))
        );
    }

    public function test_cambiar_contrasena_ok()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route("cambiar-contrasena"),
            [
                "contrasenaActual" => "password",
                "nuevaContrasena" => "nuevapassword",
                "nuevaContrasena_confirmation" => "nuevapassword"
            ]);
        $response->assertStatus(200);
    }
}
