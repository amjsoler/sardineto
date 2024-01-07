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
        Schema::create('ejercicios', function (Blueprint $table) {
            $table->id();

            $table->string("nombre", 150)->required();
            $table->string("descripcion", 500)->nullable();
            $table->string("demostracion")->nullable();

            $table->unsignedBigInteger("gimnasio");
            $table->foreign("gimnasio")->references("id")->on("gimnasios");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejercicios');
    }
};
