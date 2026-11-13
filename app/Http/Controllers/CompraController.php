<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(): View
    {
        $compras = Compra::with(['proveedor', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10);

        return view('compras.index', compact('compras'));
    }

    public function create(): View
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(StoreCompraRequest $request): RedirectResponse
    {
        $finalizar = $request->boolean('finalizar');

        DB::transaction(function () use ($request, $finalizar) {
            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'user_id' => $request->user()->id,
                'fecha' => $request->fecha,
                'total' => 0,
                'estado' => $finalizar ? Compra::ESTADO_ACTIVO : Compra::ESTADO_EN_PROGRESO,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $cantidad = (int) $item['cantidad'];
                $costo = (float) $item['costo_unitario'];
                $subtotal = $cantidad * $costo;

                $compra->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;

                if ($finalizar) {
                    $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                    $producto->stock_actual += $cantidad;
                    $producto->precio_compra = $costo;
                    $producto->save();

                    MovimientoInventario::create([
                        'producto_id' => $producto->id,
                        'user_id' => $request->user()->id,
                        'compra_id' => $compra->id,
                        'venta_id' => null,
                        'tipo' => MovimientoInventario::TIPO_ENTRADA,
                        'cantidad' => $cantidad,
                        'fecha' => $request->fecha,
                        'observacion' => "Entrada por compra #{$compra->id}",
                    ]);
                }
            }

            $compra->update(['total' => $total]);
        });

        return redirect()->route('compras.index')
            ->with('success', $finalizar
                ? 'Entrada registrada y stock actualizado.'
                : 'Entrada guardada en progreso.');
    }

    public function show(Compra $compra): View
    {
        $compra->load(['proveedor', 'user', 'detalles.producto']);

        return view('compras.show', compact('compra'));
    }

    public function edit(Compra $compra): View|RedirectResponse
    {
        if ($compra->estado !== Compra::ESTADO_EN_PROGRESO) {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Solo las entradas en progreso se pueden editar.');
        }

        $compra->load('detalles.producto');
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $items = $compra->detalles->map(fn ($d) => [
            'producto_id' => $d->producto_id,
            'producto_nombre' => $d->producto?->nombre,
            'cantidad' => $d->cantidad,
            'costo_unitario' => (float) $d->costo_unitario,
        ])->values();

        return view('compras.edit', compact('compra', 'proveedores', 'productos', 'items'));
    }

    public function update(StoreCompraRequest $request, Compra $compra): RedirectResponse
    {
        if ($compra->estado !== Compra::ESTADO_EN_PROGRESO) {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Solo las entradas en progreso se pueden editar.');
        }

        DB::transaction(function () use ($request, $compra) {
            $compra->detalles()->delete();

            $total = 0;

            foreach ($request->items as $item) {
                $cantidad = (int) $item['cantidad'];
                $costo = (float) $item['costo_unitario'];
                $subtotal = $cantidad * $costo;

                $compra->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $compra->update([
                'proveedor_id' => $request->proveedor_id,
                'fecha' => $request->fecha,
                'total' => $total,
            ]);
        });

        return redirect()->route('compras.index')
            ->with('success', 'Entrada actualizada correctamente.');
    }

    public function finalizar(Compra $compra): RedirectResponse
    {
        if ($compra->estado !== Compra::ESTADO_EN_PROGRESO) {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Solo las entradas en progreso se pueden finalizar.');
        }

        DB::transaction(function () use ($compra) {
            $compra->load('detalles');

            foreach ($compra->detalles as $detalle) {
                $producto = Producto::lockForUpdate()->findOrFail($detalle->producto_id);

                $producto->stock_actual += $detalle->cantidad;
                $producto->precio_compra = $detalle->costo_unitario;
                $producto->save();

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'user_id' => $compra->user_id,
                    'compra_id' => $compra->id,
                    'venta_id' => null,
                    'tipo' => MovimientoInventario::TIPO_ENTRADA,
                    'cantidad' => $detalle->cantidad,
                    'fecha' => $compra->fecha,
                    'observacion' => "Entrada por compra #{$compra->id}",
                ]);
            }

            $compra->update(['estado' => Compra::ESTADO_ACTIVO]);
        });

        return redirect()->route('compras.index')
            ->with('success', 'Entrada finalizada y stock actualizado.');
    }

    public function inactivar(Request $request, Compra $compra): RedirectResponse
    {
        if ($compra->estado === Compra::ESTADO_ACTIVO && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if (! in_array($compra->estado, [Compra::ESTADO_EN_PROGRESO, Compra::ESTADO_ACTIVO], true)) {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Esta entrada no se puede inactivar.');
        }

        $motivo = null;
        if ($compra->estado === Compra::ESTADO_ACTIVO) {
            $request->validate([
                'motivo' => ['required', 'string', 'min:5'],
            ], [
                'motivo.required' => 'La razón de inactivación es obligatoria.',
                'motivo.min' => 'La razón debe tener al menos 5 caracteres.',
            ]);
            $motivo = $request->motivo;
        }

        $compra->update([
            'estado' => Compra::ESTADO_INACTIVO,
            'motivo' => $motivo,
        ]);

        return redirect()->route('compras.index')
            ->with('success', 'Entrada marcada como inactiva.');
    }
}