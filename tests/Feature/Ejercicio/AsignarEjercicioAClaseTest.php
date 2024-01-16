<?php

namespace Tests\Feature\Ejercicio;

use App\Models\Clase;
use App\Models\Ejercicio;
use App\Models\Gimnasio;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AsignarEjercicioAClaseTest extends TestCase
{
    protected $usuarioInvitado;
    protected $usuarioInvitadoYAceptado;
    protected $administrador;
    protected $propietario;
    protected $gimnasio;
    protected $gimnasio2;
    protected $ejercicio;
    protected $ejercicio2;
    protected $clase;
    protected $clase2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usuarioInvitado = User::factory()->create();
        $this->usuarioInvitadoYAceptado = User::factory()->create();
        $this->administrador = User::factory()->create();
        $this->propietario = User::factory()->create();

        $this->gimnasio = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->gimnasio2 = Gimnasio::factory()->create([
            "propietario" => $this->propietario
        ]);

        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitado);
        $this->gimnasio->usuariosInvitados()->attach($this->usuarioInvitadoYAceptado, [
            "invitacion_aceptada" => true
        ]);
        $this->ejercicio = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio]);
        $this->ejercicio2 = Ejercicio::factory()->create(["gimnasio" => $this->gimnasio2]);

        $this->gimnasio->administradores()->attach($this->administrador);

        $this->clase = Clase::factory()->create(["gimnasio" => $this->gimnasio]);
        $this->clase2 = Clase::factory()->create(["gimnasio" => $this->gimnasio2]);
    }

    public function test_asignar_ejercicio_a_clase_sin_autenticacion()
    {
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(401);
    }

    public function test_asignar_ejercicio_a_clase_sin_verificar_cuenta()
    {
        $usuarioSinVerificar = User::factory()->create([
            "email_verified_at" => null
        ]);
        $this->actingAs($usuarioSinVerificar);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(460);
    }
    public function test_asignar_ejercicio_a_clase_sin_autorizacion()
    {
        $this->actingAs($this->usuarioInvitado);
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->usuarioInvitadoYAceptado);
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);

        $this->actingAs($this->administrador);
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(200);

        $this->actingAs($this->propietario);
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(422);

        //Comprobar ejercicio pertenece a gim
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio2->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio2->id
        ]));
        $response->assertStatus(403);

        //Comprobamos clase pertenece a gim
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio2->id,
            "clase" => $this->clase2->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(403);
    }
    public function test_asignar_ejercicio_a_clase_not_found_route_params()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => Gimnasio::orderBy("id", "desc")->first()->id+1,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(404);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => Clase::orderBy("id", "desc")->first()->id+1,
            "ejercicio" => $this->ejercicio->id
        ]));
        $response->assertStatus(404);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => Ejercicio::orderBy("id", "desc")->first()->id+1
        ]));
        $response->assertStatus(404);
    }
    public function test_asignar_ejercicio_a_clase_validation_fail()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "detalles" => Str::random(101)
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.detalles.0", __("validation.ejercicio.detalles.max"))
        );

        //Asignación OK
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "detalles" => "detalles del ejer en la clase"
        ]);
        $response->assertStatus(200);

        //Vuelvo a intentar asignar el mismo ejercicio a la clase
        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "detalles" => "detalles del ejer en la clase"
        ]);
        $response->assertStatus(422);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has("message")
            ->where("errors.ejercicio.0", __("validation.ejercicio.ejercicio.unique"))
        );

        //Ahora creo una clase y le intento asignar el mismo ejercicio (debería poder)
        $claseRand = Clase::factory()->create(["gimnasio" => $this->gimnasio->id]);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $claseRand->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "detalles" => "detalles del ejer en la clase"
        ]);
        $response->assertStatus(200);
    }
    public function test_asignar_ejercicio_a_clase_ok()
    {
        $this->actingAs($this->propietario);

        $response = $this->postJson(route("asociar-ejercicio", [
            "gimnasio" => $this->gimnasio->id,
            "clase" => $this->clase->id,
            "ejercicio" => $this->ejercicio->id
        ]), [
            "detalles" => "detalles del ejer en la clase"
        ]);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where("id", $this->clase->id)
            ->etc()
            ->has("ejercicios", 1)
            ->has("ejercicios.0", fn (AssertableJson $json) => $json
                ->where("id", $this->ejercicio->id)
                ->where("nombre", $this->ejercicio->nombre)
                ->where("descripcion", $this->ejercicio->descripcion)
                ->where("demostracion", $this->ejercicio->demostracion)
                ->where("gimnasio", $this->gimnasio->id)
                ->has("pivot", fn(AssertableJson $json) => $json
                    ->where("clase", $this->clase->id)
                    ->where("detalles", "detalles del ejer en la clase")
                    ->where("ejercicio", $this->ejercicio->id)
                )
            )
        );
    }
}
