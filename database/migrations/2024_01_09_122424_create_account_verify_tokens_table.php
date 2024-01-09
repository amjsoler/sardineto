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
        Schema::create('account_verify_tokens', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("usuario")->unique();
            $table->foreign("usuario")->references("id")->on("users")->cascadeOnDelete();

            $table->string("token");
            $table->timestamp("valido_hasta");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_verify_tokens');
    }
};
