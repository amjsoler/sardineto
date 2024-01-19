<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();

            $table->string("nombre", 150)->required();
            $table->unsignedDecimal("precio")->required();
            $table->unsignedInteger("creditos")->required();

            $table->enum("tipo", ["suscripcion", "abono"])->default("suscripcion");

            $table->unsignedBigInteger("gimnasio")->required();
            $table->foreign("gimnasio")->references("id")->on("gimnasios")->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
