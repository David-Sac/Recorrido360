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
        Schema::create('recorrido_panorama', function (Blueprint $table) {
            $table->foreignId('recorrido_id')->constrained()->cascadeOnDelete();
            $table->foreignId('panorama_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('orden');
            $table->timestamps();

            $table->primary(['recorrido_id','panorama_id']);
            $table->unique(['recorrido_id','orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorrido_panorama');
    }
};
