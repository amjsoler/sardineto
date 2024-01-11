<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VerGimnasiosTest extends TestCase
{
    public function test_ver_gimnasio_sin_autenticar(): void
    {
        $response = $this->get(route("mis-gimnasios"),
        [
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ]);

        $response->assertStatus(401);
    }

    public function test_ver_gimnasio_cuenta_sin_verificar(): void
    {
        $usuario = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuario);

        $response = $this->get(route("mis-gimnasios"),
            [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]);

        $response->assertStatus(460);
    }

    public function test_ver_mis_gimnasios_ok(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        $response = $this->get(route("mis-gimnasios"),
            [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has(0)
        );
    }

    //Aquí también revisto que los campos que se devuelven son los que quiero y no hay de más como pj el propietario
    public function test_ver_mis_gimnasios_con_gimnasio_propietario_ok(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);
        $gimnasio = Gimnasio::factory()->make();
        $gimnasio->propietario = $usuario->id;
        $gimnasio->save();
        $response = $this->get(route("mis-gimnasios"),
            [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has(1)
            ->first(fn(AssertableJson $json) =>
                $json->where("id", $gimnasio->id)
                ->where("nombre", $gimnasio->nombre)
                ->hasAll(["nombre", "descripcion", "logo", "direccion"])
            )
        );
    }

    public function test_ver_mis_gimnasios_con_dos_gimnasios_uno_propietario_otro_invitado_ok(): void
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();

        $this->actingAs($usuario1);

        $gimnasio2 = Gimnasio::factory()->make([
            "nombre" => "fdsa"
        ]);
        $gimnasio2->propietario = $usuario2->id;
        $gimnasio2->save();
        $gimnasio2->usuariosInvitados()->attach($usuario1->id, ["invitacion_aceptada" => 1]);

        $gimnasio1 = Gimnasio::factory()->make([
            "nombre" => "asdf"
        ]);
        $gimnasio1->propietario = $usuario1->id;
        $gimnasio1->save();

        $response = $this->get(route("mis-gimnasios"),
            [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJson(fn(AssertableJson $json) =>
            $json->first(fn (AssertableJson $json) =>
                $json->where("nombre", "asdf")
                ->etc()
            )
            ->etc()
        );
    }

}
