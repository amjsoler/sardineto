<?php

namespace Tests\Feature\Gimnasio;

use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebAceptarInvitacionTest extends TestCase
{
    public function test_aceptar_invitacion_a_gimnasio_con_todo_inventado()
    {
        $response = $this->get(route("aceptar-invitacion",
            [
                "gimnasio" => 1,
                "hash" => "inventhash"
            ]));

        $response->assertOk();
        $response->assertViewIs("gimnasio.invitacionAceptada");
        $response->assertViewHas("response");
        $response->assertSee(__("vistas.gimnasio.invitacionAceptada.ko1"));
    }

    public function test_aceptar_invitacion_a_gimnasio_creando_gimnasio_y_token()
    {
        $propietario = User::factory()->create();
        $gimnasio = Gimnasio::factory()->create(["propietario" => $propietario->id]);

        $token = Hash::make(now());

        $propietario->gimnasiosInvitado()->attach($gimnasio, [
            "token_aceptacion" => $token,
            "invitacion_aceptada" => 0
        ]);

        $response = $this->get(route("aceptar-invitacion",
            [
                "gimnasio" => $gimnasio->id,
                "hash" => $token
            ]));

        $response->assertOk();
        $response->assertViewIs("gimnasio.invitacionAceptada");
        $response->assertViewHas("response");
        $response->assertSee(__("vistas.gimnasio.invitacionAceptada.ok1"));
    }
}
