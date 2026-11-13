<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AjusteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ClienteAbonosController;
use App\Http\Controllers\ClienteCatalogoController;
use App\Http\Controllers\ClienteDashboardController;
use App\Http\Controllers\ClienteVentasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
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

    // Consulta de productos disponible para admin y empleado.
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

    // Entradas de mercancía (compras): registro y consulta para admin y empleado.
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    Route::get('/compras/{compra}/edit', [CompraController::class, 'edit'])->name('compras.edit');
    Route::put('/compras/{compra}', [CompraController::class, 'update'])->name('compras.update');
    Route::post('/compras/{compra}/finalizar', [CompraController::class, 'finalizar'])->name('compras.finalizar');
    Route::patch('/compras/{compra}/inactivar', [CompraController::class, 'inactivar'])->name('compras.inactivar');

    // Ajustes de inventario.
    Route::get('/ajustes', [AjusteController::class, 'index'])->name('ajustes.index');
    Route::get('/ajustes/create', [AjusteController::class, 'create'])->name('ajustes.create');
    Route::post('/ajustes', [AjusteController::class, 'store'])->name('ajustes.store');
    Route::get('/ajustes/{ajuste}', [AjusteController::class, 'show'])->name('ajustes.show');
    Route::get('/ajustes/{ajuste}/edit', [AjusteController::class, 'edit'])->name('ajustes.edit');
    Route::put('/ajustes/{ajuste}', [AjusteController::class, 'update'])->name('ajustes.update');
    Route::post('/ajustes/{ajuste}/finalizar', [AjusteController::class, 'finalizar'])->name('ajustes.finalizar');
    Route::patch('/ajustes/{ajuste}/inactivar', [AjusteController::class, 'inactivar'])->name('ajustes.inactivar');

    Route::get('/movimientos', [MovimientoInventarioController::class, 'index'])->name('movimientos.index');

    // Gestionar categorías y productos es exclusivo del administrador (matriz de permisos).
    Route::middleware('rol:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class)->except(['show']);
        Route::resource('proveedores', ProveedorController::class)
            ->parameters(['proveedores' => 'proveedor'])
            ->except(['show']);

        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::patch('/productos/{producto}/toggle-activo', [ProductoController::class, 'toggleActivo'])->name('productos.toggleActivo');
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
