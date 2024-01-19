<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_participan_clases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("usuario");
            $table->foreign("usuario")->references("id")->on("users")->cascadeOnDelete();

            $table->unsignedBigInteger("clase");
            $table->foreign("clase")->references("id")->on("clases")->cascadeOnDelete();

            $table->unsignedBigInteger("suscripcion");
            $table->foreign("suscripcion")->references("id")->on("suscripciones")->cascadeOnDelete();

            $table->unique(["usuario", "clase"]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_participan_clases');
    }
};
