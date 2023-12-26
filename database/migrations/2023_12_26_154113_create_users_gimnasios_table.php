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
        Schema::create('users_gimnasios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("usuario");
            $table->foreign("usuario")->references("id")->on("users");

            $table->unsignedBigInteger("gimnasio");
            $table->foreign("gimnasio")->references("id")->on("gimnasios");

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
        Schema::dropIfExists('users_gimnasios');
    }
};
