<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// La raíz del sitio lleva al panel principal.
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'rol:admin,empleado'])->group(function () {
    // Panel principal con indicadores del minimercado.
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Perfil del usuario (Breeze).
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestionar categorías es exclusivo del administrador (matriz de permisos).
    Route::middleware('rol:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
