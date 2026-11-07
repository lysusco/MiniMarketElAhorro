<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con los indicadores del minimercado.
     */
    public function __invoke(Request $request): View
    {
        $hoy = now()->toDateString();

        // Indicadores generales.
        $productosActivos = Producto::where('activo', true)->count();
        $productosStockBajo = Producto::where('activo', true)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();
        $ventasHoy = Venta::whereDate('fecha', $hoy);
        $carteraPendiente = (float) Cliente::where('activo', true)->sum('saldo');

        // Últimas ventas registradas.
        $ultimasVentas = Venta::with(['cliente', 'user'])
            ->latest('id')
            ->take(5)
            ->get();

        // Productos que requieren reposición (RF-11).
        $stockBajo = Producto::with('categoria')
            ->where('activo', true)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')
            ->take(5)
            ->get();

        return view('dashboard', [
            'productosActivos' => $productosActivos,
            'productosStockBajo' => $productosStockBajo,
            'ventasHoyCantidad' => (clone $ventasHoy)->count(),
            'ventasHoyTotal' => (float) (clone $ventasHoy)->sum('total'),
            'carteraPendiente' => $carteraPendiente,
            'ultimasVentas' => $ultimasVentas,
            'stockBajo' => $stockBajo,
        ]);
    }
}
