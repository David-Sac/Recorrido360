<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recorridos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();

            // ✅ reemplaza cualquier 'estado_publicacion' por:
            $table->boolean('publicado')->default(false);

            // ✅ autor opcional (no romperá al crear). Se guarda desde el controlador.
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recorridos');
    }
};
