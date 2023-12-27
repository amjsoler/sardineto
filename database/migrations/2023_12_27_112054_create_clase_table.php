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
        Schema::create('clases', function (Blueprint $table) {
            $table->id();

            $table->string("nombre", 150)->required();
            $table->text("descripcion", 5000)->nullable();
            $table->timestamp("fechayhora")->required();
            $table->unsignedInteger("plazas")->required();

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
        Schema::dropIfExists('clases');
    }
};
