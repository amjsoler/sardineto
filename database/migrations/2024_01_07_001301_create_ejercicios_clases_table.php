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
        Schema::create('ejercicios_clases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("gimnasio");
            $table->foreign("gimnasio")->references("id")->on("gimnasios");

            $table->unsignedBigInteger("clase");
            $table->foreign("clase")->references("id")->on("clases");

            $table->unsignedBigInteger("ejercicio");
            $table->foreign("ejercicio")->references("id")->on("ejercicios");

            $table->unique(["gimnasio", "clase", "ejercicio"]);

            $table->string("detalles", 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejercicios_clases');
    }
};
