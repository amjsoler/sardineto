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
        Schema::create('usuarios_compran_articulos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("articulo")->required();
            $table->foreign("articulo")->references("id")->on("articulos")->cascadeOnDelete();

            $table->unsignedBigInteger("usuario")->required();
            $table->foreign("usuario")->references("id")->on("users")->cascadeOnDelete();

            $table->unsignedBigInteger("gimnasio")->required();
            $table->foreign("gimnasio")->references("id")->on("gimnasios")->cascadeOnDelete();

            $table->dateTime("pagada")->nullable()->default(null);
            $table->dateTime("entregada")->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_compran_articulos');
    }
};
