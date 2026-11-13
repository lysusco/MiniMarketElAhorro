<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteAbonosController;
use App\Http\Controllers\ClienteCatalogoController;
use App\Http\Controllers\ClienteDashboardController;
use App\Http\Controllers\ClienteVentasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// La raíz del sitio redirige según el rol del usuario autenticado.
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('dashboard');
    }

    $user = auth()->user();

    if ($user->isCliente()) {
        return redirect()->route('cliente.panel');
    }

    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    // Perfil del usuario (Breeze), disponible para todos los roles.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'rol:admin,empleado'])->group(function () {
    // Panel principal con indicadores del minimercado.
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Gestionar categorías es exclusivo del administrador (matriz de permisos).
    Route::middleware('rol:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class)->except(['show']);
    });
});

Route::middleware(['auth', 'rol:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/panel', ClienteDashboardController::class)->name('panel');
    Route::get('/catalogo', [ClienteCatalogoController::class, 'index'])->name('catalogo');
    Route::get('/ventas', [ClienteVentasController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/{venta}', [ClienteVentasController::class, 'show'])->name('ventas.show');
    Route::get('/abonos', [ClienteAbonosController::class, 'index'])->name('abonos.index');
});

require __DIR__.'/auth.php';
