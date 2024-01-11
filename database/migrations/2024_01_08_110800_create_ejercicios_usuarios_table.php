<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ejercicios_usuarios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("usuario");
            $table->foreign("usuario")->references("id")->on("users")->cascadeOnDelete();

            $table->unsignedBigInteger("ejercicio");
            $table->foreign("ejercicio")->references("id")->on("ejercicios")->cascadeOnDelete();

            $table->float("unorm")->required();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejercicios_usuarios');
    }
};
