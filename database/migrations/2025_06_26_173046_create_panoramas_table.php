<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Ejecuta la migración: crea la tabla `panoramas` */
    public function up(): void
    {
        Schema::create('panoramas', function (Blueprint $table) {
            $table->id();                        // PK
            $table->string('nombre');            // Nombre del panorama
            $table->string('imagen_path');       // Ruta al archivo 360°
            
            // <-- Añadimos aquí la relación al componente padre:
            $table->foreignId('componente_id')   // BIGINT UNSIGNED
                  ->constrained('componentes')  // FK → componentes.id
                  ->onDelete('cascade');        // Si borras el componente, borra sus panoramas
            
            $table->foreignId('created_by')      // Usuario que creó este panorama
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();                // created_at + updated_at
        });
    }

    /** Revierte la migración: borra la tabla `panoramas` */
    public function down(): void
    {
        Schema::dropIfExists('panoramas');
    }
};
