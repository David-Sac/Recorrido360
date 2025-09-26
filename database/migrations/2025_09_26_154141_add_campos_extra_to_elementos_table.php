<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            // Campos adicionales
            $table->string('titulo')->nullable()->after('tipo');
            $table->text('descripcion')->nullable()->after('titulo');

            // Puede ser una URL externa (YouTube, image, etc.)
            $table->string('url')->nullable()->after('descripcion');

            // O un archivo subido en storage
            $table->string('media_path')->nullable()->after('url');

            // Metadatos en JSON para flexibilidad
            $table->json('meta')->nullable()->after('media_path');
        });
    }

    public function down(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'descripcion', 'url', 'media_path', 'meta']);
        });
    }
};
