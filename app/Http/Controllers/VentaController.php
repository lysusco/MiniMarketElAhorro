<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $ventas = Venta::with(['cliente', 'user'])
            ->when($request->filled('fecha_desde'), function ($query) use ($request) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            })
            ->when($request->filled('fecha_hasta'), function ($query) use ($request) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            })
            ->when($request->filled('tipo_venta'), function ($query) use ($request) {
                $query->where('tipo_venta', $request->tipo_venta);
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('ventas.index', compact('ventas'));
    }

    public function create(): View
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();

        return view('ventas.create', compact('productos', 'clientes'));
    }

    public function store(StoreVentaRequest $request): RedirectResponse
    {
        $tipoVenta = $request->tipo_venta;
        $descuento = round((float) ($request->descuento ?? 0), 2);

        // Agrega las cantidades de los items repetidos por producto (regla 2).
        $cantidades = [];
        foreach ($request->items as $item) {
            $productoId = (int) $item['producto_id'];
            $cantidades[$productoId] = ($cantidades[$productoId] ?? 0) + (int) $item['cantidad'];
        }

        DB::transaction(function () use ($request, $tipoVenta, $descuento, $cantidades) {
            // Bloquea los productos para que dos ventas simultáneas
            // no puedan vender el mismo stock dos veces (RF-15).
            $productos = [];
            foreach ($cantidades as $productoId => $cantidad) {
                $producto = Producto::lockForUpdate()->findOrFail($productoId);

                if (! $producto->activo) {
                    throw ValidationException::withMessages([
                        'items' => "El producto {$producto->nombre} está inactivo y no se puede vender.",
                    ]);
                }

                if ($producto->stock_actual < $cantidad) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$producto->nombre}: disponible {$producto->stock_actual}, solicitado {$cantidad}",
                    ]);
                }

                $productos[$productoId] = $producto;
            }

            $subtotal = 0.0;
            foreach ($productos as $producto) {
                $subtotal += $cantidades[$producto->id] * (float) $producto->precio_venta;
            }
            $subtotal = round($subtotal, 2);

            if ($descuento > $subtotal) {
                throw ValidationException::withMessages([
                    'descuento' => 'El descuento no puede superar el subtotal.',
                ]);
            }

            $total = round($subtotal - $descuento, 2);

            // En ventas fiadas se valida el cliente y su crédito (reglas 5 y 6).
            $cliente = null;
            if ($tipoVenta === Venta::TIPO_FIADO) {
                $cliente = Cliente::findOrFail($request->cliente_id);

                if (! $cliente->activo) {
                    throw ValidationException::withMessages([
                        'cliente_id' => 'El cliente seleccionado no está activo.',
                    ]);
                }

                if (round((float) $cliente->saldo + $total, 2) > (float) $cliente->limite_credito) {
                    $saldo = number_format((float) $cliente->saldo, 2, '.', '');
                    $disponible = number_format($cliente->creditoDisponible(), 2, '.', '');
                    $totalVenta = number_format($total, 2, '.', '');

                    throw ValidationException::withMessages([
                        'cliente_id' => "El cliente tiene saldo {$saldo} y cupo disponible {$disponible}; la venta de {$totalVenta} supera su límite de crédito.",
                    ]);
                }
            }

            $venta = Venta::create([
                'cliente_id' => $cliente?->id,
                'user_id' => $request->user()->id,
                'fecha' => $request->fecha,
                'tipo_venta' => $tipoVenta,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => $tipoVenta === Venta::TIPO_FIADO
                    ? Venta::ESTADO_PENDIENTE
                    : Venta::ESTADO_PAGADA,
            ]);

            foreach ($cantidades as $productoId => $cantidad) {
                $producto = $productos[$productoId];

                // Precio histórico: conserva el precio de venta al momento de la venta.
                $venta->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => round($cantidad * (float) $producto->precio_venta, 2),
                ]);

                $producto->stock_actual -= $cantidad;
                $producto->save();

                // Regla 12: cada cambio de stock genera un movimiento de inventario.
                MovimientoInventario::create([
                    'producto_id' => $productoId,
                    'user_id' => $request->user()->id,
                    'compra_id' => null,
                    'venta_id' => $venta->id,
                    'tipo' => MovimientoInventario::TIPO_SALIDA,
                    'cantidad' => $cantidad,
                    'fecha' => $request->fecha,
                    'observacion' => "Salida por venta #{$venta->id}",
                ]);
            }

            if ($cliente !== null) {
                $cliente->saldo = round((float) $cliente->saldo + $total, 2);
                $cliente->save();
            }
        });

        return redirect()->route('ventas.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta): View
    {
        $venta->load(['detalles.producto', 'abonos.user', 'cliente', 'user']);

        return view('ventas.show', compact('venta'));
    }
}
