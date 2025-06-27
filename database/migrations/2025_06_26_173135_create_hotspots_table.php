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
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();

            // FK a panoramas.id
            $table->foreignId('panorama_id')
                  ->constrained('panoramas')
                  ->onDelete('cascade');

            // FK a elementos.id (en lugar de componente_id)
            $table->foreignId('elemento_id')
                  ->constrained('elementos')
                  ->onDelete('cascade');

            // Posición dentro de la esfera: "x y z"
            $table->string('posicion', 50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
