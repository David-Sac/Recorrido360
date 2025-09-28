<?php
// database/migrations/2025_09_27_000000_cleanup_elementos_drop_url_titulo_meta.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            if (Schema::hasColumn('elementos', 'url')) {
                $table->dropColumn('url');
            }
            if (Schema::hasColumn('elementos', 'titulo')) {
                $table->dropColumn('titulo');
            }
            if (Schema::hasColumn('elementos', 'meta')) {
                $table->dropColumn('meta');
            }

            if (!Schema::hasColumn('elementos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('elementos', 'contenido')) {
                $table->longText('contenido')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('elementos', 'media_path')) {
                $table->string('media_path')->nullable()->after('contenido');
            }
        });
    }

    public function down(): void
    {
        Schema::table('elementos', function (Blueprint $table) {
            if (!Schema::hasColumn('elementos', 'url')) {
                $table->string('url', 2048)->nullable()->after('media_path');
            }
            if (!Schema::hasColumn('elementos', 'titulo')) {
                $table->string('titulo')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('elementos', 'meta')) {
                $table->json('meta')->nullable()->after('url');
            }
        });
    }
};
