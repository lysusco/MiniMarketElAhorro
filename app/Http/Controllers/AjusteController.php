<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAjusteRequest;
use App\Models\Ajuste;
use App\Models\DetalleAjuste;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AjusteController extends Controller
{
    public function index(): View
    {
        $ajustes = Ajuste::with(['user', 'detalles.producto'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15);

        return view('ajustes.index', compact('ajustes'));
    }

    public function create(): View
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('ajustes.create', compact('productos'));
    }

    public function store(StoreAjusteRequest $request): RedirectResponse
    {
        $finalizar = $request->boolean('finalizar');

        foreach ($request->items as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            if ($producto->stock_actual < $item['cantidad']) {
                return redirect()->back()
                    ->withErrors(['items' => 'La cantidad de '.$producto->nombre.' supera el stock disponible.'])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request, $finalizar) {
            $ajuste = Ajuste::create([
                'user_id' => $request->user()->id,
                'fecha' => now()->toDateString(),
                'motivo' => $request->motivo,
                'observacion' => $request->observacion,
                'estado' => $finalizar ? Ajuste::ESTADO_ACTIVO : Ajuste::ESTADO_EN_PROGRESO,
            ]);

            foreach ($request->items as $item) {
                DetalleAjuste::create([
                    'ajuste_id' => $ajuste->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => (int) $item['cantidad'],
                ]);

                if ($finalizar) {
                    $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                    $producto->stock_actual -= (int) $item['cantidad'];
                    $producto->save();

                    MovimientoInventario::create([
                        'producto_id' => $producto->id,
                        'user_id' => $request->user()->id,
                        'compra_id' => null,
                        'venta_id' => null,
                        'tipo' => MovimientoInventario::TIPO_AJUSTE,
                        'cantidad' => (int) $item['cantidad'],
                        'fecha' => now()->toDateString(),
                        'observacion' => 'Ajuste por '.$request->motivo.
                            ($request->observacion ? ': '.$request->observacion : ''),
                    ]);
                }
            }
        });

        return redirect()->route('ajustes.index')
            ->with('success', $finalizar
                ? 'Ajuste registrado y stock actualizado.'
                : 'Ajuste guardado en progreso.');
    }

    public function show(Ajuste $ajuste): View
    {
        $ajuste->load(['user', 'detalles.producto']);

        return view('ajustes.show', compact('ajuste'));
    }

    public function edit(Ajuste $ajuste): View|RedirectResponse
    {
        if ($ajuste->estado !== Ajuste::ESTADO_EN_PROGRESO) {
            return redirect()->route('ajustes.show', $ajuste)
                ->with('error', 'Solo los ajustes en progreso se pueden editar.');
        }

        $ajuste->load('detalles.producto');
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $items = $ajuste->detalles->map(fn ($d) => [
            'producto_id' => $d->producto_id,
            'producto_nombre' => $d->producto?->nombre,
            'cantidad' => $d->cantidad,
        ])->values();

        return view('ajustes.edit', compact('ajuste', 'productos', 'items'));
    }

    public function update(StoreAjusteRequest $request, Ajuste $ajuste): RedirectResponse
    {
        if ($ajuste->estado !== Ajuste::ESTADO_EN_PROGRESO) {
            return redirect()->route('ajustes.show', $ajuste)
                ->with('error', 'Solo los ajustes en progreso se pueden editar.');
        }

        foreach ($request->items as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            if ($producto->stock_actual < $item['cantidad']) {
                return redirect()->back()
                    ->withErrors(['items' => 'La cantidad de '.$producto->nombre.' supera el stock disponible.'])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request, $ajuste) {
            $ajuste->detalles()->delete();

            foreach ($request->items as $item) {
                DetalleAjuste::create([
                    'ajuste_id' => $ajuste->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => (int) $item['cantidad'],
                ]);
            }

            $ajuste->update([
                'motivo' => $request->motivo,
                'observacion' => $request->observacion,
            ]);
        });

        return redirect()->route('ajustes.index')
            ->with('success', 'Ajuste actualizado correctamente.');
    }

    public function finalizar(Ajuste $ajuste): RedirectResponse
    {
        if ($ajuste->estado !== Ajuste::ESTADO_EN_PROGRESO) {
            return redirect()->route('ajustes.show', $ajuste)
                ->with('error', 'Solo los ajustes en progreso se pueden finalizar.');
        }

        foreach ($ajuste->detalles as $detalle) {
            $producto = Producto::findOrFail($detalle->producto_id);
            if ($producto->stock_actual < $detalle->cantidad) {
                return redirect()->route('ajustes.show', $ajuste)
                    ->with('error', 'La cantidad de '.$producto->nombre.' supera el stock disponible.');
            }
        }

        DB::transaction(function () use ($ajuste) {
            $ajuste->load('detalles');

            foreach ($ajuste->detalles as $detalle) {
                $producto = Producto::lockForUpdate()->findOrFail($detalle->producto_id);
                $producto->stock_actual -= $detalle->cantidad;
                $producto->save();

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'user_id' => $ajuste->user_id,
                    'compra_id' => null,
                    'venta_id' => null,
                    'tipo' => MovimientoInventario::TIPO_AJUSTE,
                    'cantidad' => $detalle->cantidad,
                    'fecha' => $ajuste->fecha,
                    'observacion' => 'Ajuste por '.$ajuste->motivo.
                        ($ajuste->observacion ? ': '.$ajuste->observacion : ''),
                ]);
            }

            $ajuste->update(['estado' => Ajuste::ESTADO_ACTIVO]);
        });

        return redirect()->route('ajustes.index')
            ->with('success', 'Ajuste finalizado y stock actualizado.');
    }

    public function inactivar(Request $request, Ajuste $ajuste): RedirectResponse
    {
        if ($ajuste->estado === Ajuste::ESTADO_ACTIVO && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if (! in_array($ajuste->estado, [Ajuste::ESTADO_EN_PROGRESO, Ajuste::ESTADO_ACTIVO], true)) {
            return redirect()->route('ajustes.show', $ajuste)
                ->with('error', 'Este ajuste no se puede inactivar.');
        }

        $motivo = null;
        if ($ajuste->estado === Ajuste::ESTADO_ACTIVO) {
            $request->validate([
                'motivo' => ['required', 'string', 'min:5'],
            ], [
                'motivo.required' => 'La razón de inactivación es obligatoria.',
                'motivo.min' => 'La razón debe tener al menos 5 caracteres.',
            ]);
            $motivo = $request->motivo;
        }

        $ajuste->update([
            'estado' => Ajuste::ESTADO_INACTIVO,
            'motivo_inactivacion' => $motivo,
        ]);

        return redirect()->route('ajustes.index')
            ->with('success', 'Ajuste marcado como inactivo.');
    }
}