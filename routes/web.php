<?php

use App\Http\Controllers\ProfileController;
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
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    
    $components = Componente::all();

    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:Admin|Super Admin'])->group(function(){
    Route::resource('componentes', ComponenteController::class);
});

Route::middleware(['auth','role:Admin|Super Admin'])
     ->group(function () {
         Route::resource('elementos', ElementoController::class);
     });

Route::middleware(['auth','role:Admin|Super Admin'])->group(function(){
    // CRUD de panoramas
    Route::resource('panoramas', PanoramaController::class);

    // Hotspots anidados bajo panoramas
    Route::resource('panoramas.hotspots', HotspotController::class)
         ->shallow()
         ->only(['index','store','destroy']);
});

Route::post('panoramas/{panorama}/hotspots', [HotspotController::class,'store'])
     ->middleware(['auth','role:Admin|Super Admin']);
