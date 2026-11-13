<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\View\View;

class MovimientoInventarioController extends Controller
{
    public function index(): View
    {
        $movimientos = MovimientoInventario::with(['producto', 'user', 'compra', 'venta'])
            ->when(request('producto_id'), function ($query, $productoId) {
                $query->where('producto_id', $productoId);
            })
            ->when(request('tipo'), function ($query, $tipo) {
                $query->where('tipo', $tipo);
            })
            ->when(request('desde'), function ($query, $desde) {
                $query->whereDate('fecha', '>=', $desde);
            })
            ->when(request('hasta'), function ($query, $hasta) {
                $query->whereDate('fecha', '<=', $hasta);
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $productos = Producto::orderBy('nombre')->get();

        return view('movimientos.index', compact('movimientos', 'productos'));
    }
}