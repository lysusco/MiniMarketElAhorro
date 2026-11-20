<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    /**
     * Reporte de ventas por rango de fechas (solo lectura).
     *
     * Muestra el listado paginado de ventas del rango y los totales
     * del rango completo: cantidad de ventas, total, contado y fiado.
     */
    public function ventas(Request $request): View
    {
        $request->validate([
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date'],
        ]);

        $aplicarRango = function ($query) use ($request): void {
            $query
                ->when($request->filled('fecha_desde'), function ($q) use ($request) {
                    $q->whereDate('fecha', '>=', $request->fecha_desde);
                })
                ->when($request->filled('fecha_hasta'), function ($q) use ($request) {
                    $q->whereDate('fecha', '<=', $request->fecha_hasta);
                });
        };

        $ventas = Venta::with(['cliente', 'user']);
        $aplicarRango($ventas);
        $ventas = $ventas
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // Totales calculados sobre el rango completo, no solo la página visible.
        $rango = Venta::query();
        $aplicarRango($rango);

        $totales = [
            'cantidad' => (clone $rango)->count(),
            'total' => (float) (clone $rango)->sum('total'),
            'contado' => (float) (clone $rango)->where('tipo_venta', Venta::TIPO_CONTADO)->sum('total'),
            'fiado' => (float) (clone $rango)->where('tipo_venta', Venta::TIPO_FIADO)->sum('total'),
        ];

        return view('reportes.ventas', compact('ventas', 'totales'));
    }

    /**
     * Cartera de clientes con saldo pendiente (solo lectura).
     *
     * Lista los clientes con saldo mayor que cero, ordenados de mayor a
     * menor saldo, con su límite, crédito disponible y la cantidad de
     * ventas fiadas aún pendientes de pago.
     */
    public function cartera(): View
    {
        $clientes = Cliente::where('saldo', '>', 0)
            ->withCount([
                'ventas as ventas_con_saldo_pendiente' => function ($query) {
                    $query
                        ->where('tipo_venta', Venta::TIPO_FIADO)
                        ->whereIn('estado', [Venta::ESTADO_PENDIENTE, Venta::ESTADO_PARCIAL]);
                },
            ])
            ->orderByDesc('saldo')
            ->get();

        return view('reportes.cartera', compact('clientes'));
    }
}
