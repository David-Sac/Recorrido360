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
        Schema::create('elementos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('componente_id')
                    ->constrained()
                    ->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo',['datos', 'video', 'imagen', 'audio', 'otro']);
            $table->text('contenido')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elementos');
    }
};
