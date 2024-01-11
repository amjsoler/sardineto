<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios_gimnasios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("usuario")->required();
            $table->foreign("usuario")->references("id")->on("users")->cascadeOnDelete();

            $table->unsignedBigInteger("gimnasio")->required();
            $table->foreign("gimnasio")->references("id")->on("gimnasios")->cascadeOnDelete();

            $table->string("token_aceptacion")->default(str_replace("/", "", Hash::make(now())));
            $table->boolean("invitacion_aceptada")->default(false);

            $table->unique(["usuario", "gimnasio"]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_gimnasios');
    }
};
