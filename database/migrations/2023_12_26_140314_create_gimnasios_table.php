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
        Schema::create('gimnasios', function (Blueprint $table) {
            $table->id();

            $table->string("nombre", 150)->required();
            $table->text("descripcion", 5000)->nullable();
            $table->string("logo")->nullable();
            $table->string("direccion", 200)->nullable();

            $table->unsignedBigInteger("propietario");
            $table->foreign("propietario")->references("id")->on("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gimnasios');
    }
};
