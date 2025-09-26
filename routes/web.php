<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecorridoController;
use Illuminate\Support\Facades\Route;
use App\Models\Componente;
use App\Http\Controllers\ComponenteController;
use App\Http\Controllers\ElementoController;
use App\Http\Controllers\PanoramaController;
use App\Http\Controllers\HotspotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí registras las rutas web de tu aplicación.
|
*/

// Página inicial
Route::get('/', function () {
    $components = Componente::all();
    return view('welcome', compact('components'));
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación (Breeze/Jetstream)
require __DIR__.'/auth.php';

// ----------------------
// ÁREA ADMIN | SUPER ADMIN
// ----------------------
Route::middleware(['auth', 'role:Admin|Super Admin'])->group(function () {
    Route::resource('componentes', ComponenteController::class)->except(['show']);
    Route::resource('elementos',  ElementoController::class)->except(['show']);
    Route::resource('recorridos', RecorridoController::class)->except(['show']);
        // ➕ Añadir un panorama al recorrido (lo pone al final)
    Route::post('recorridos/{recorrido}/panoramas/{panorama}', [RecorridoController::class, 'attachPanorama'])
        ->name('recorridos.attach');

    // ➖ Quitar un panorama del recorrido
    Route::delete('recorridos/{recorrido}/panoramas/{panorama}', [RecorridoController::class, 'detachPanorama'])
        ->name('recorridos.detach');

    // ↕️ Guardar nuevo orden (drag & drop)
    Route::post('recorridos/{recorrido}/reordenar', [RecorridoController::class, 'reorder'])
        ->name('recorridos.reorder');

    // 🔁 Publicar / Despublicar
    Route::post('recorridos/{recorrido}/toggle-publicado', [RecorridoController::class, 'togglePublicado'])
        ->name('recorridos.toggle-publicado');
});

// ----------------------
// ÁREA ADMIN | SUPER ADMIN | ASISTENTE
// ----------------------
Route::middleware(['auth', 'role:Admin|Super Admin|Asistente'])->group(function () {
    // Si NO tienes implementado show en el controlador:
    Route::resource('panoramas', PanoramaController::class)->except(['show']);

    // Hotspots anidados con shallow (index, store anidados; destroy plano)
    Route::resource('panoramas.hotspots', HotspotController::class)
        ->shallow()
        ->only(['index', 'store', 'destroy']);
});