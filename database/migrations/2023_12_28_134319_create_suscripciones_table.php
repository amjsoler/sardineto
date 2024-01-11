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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("usuario")->required();
            $table->foreign("usuario")->references("id")->on("users")->cascadeOnDelete();

            $table->unsignedBigInteger("gimnasio")->required();
            $table->foreign("gimnasio")->references("id")->on("gimnasios")->cascadeOnDelete();

            $table->unsignedBigInteger("tarifa")->required();
            $table->foreign("tarifa")->references("id")->on("tarifas")->cascadeOnDelete();

            $table->dateTime("pagada")->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
