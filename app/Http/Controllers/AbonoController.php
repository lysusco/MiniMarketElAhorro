<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbonoRequest;
use App\Models\Abono;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AbonoController extends Controller
{
    /**
     * Muestra el formulario de registro de abonos (RF-19).
     *
     * - ?venta={id}: precarga la venta si es fiada y tiene saldo pendiente.
     * - ?cliente={id}: lista las ventas fiadas con saldo pendiente del cliente (CU-07).
     * - Sin parámetros: lista los clientes con saldo para buscar por nombre o identificación.
     */
    public function create(Request $request): View
    {
        $ventaSeleccionada = null;
        $clienteSeleccionado = null;
        $ventasCliente = collect();
        $advertencia = null;

        // Regla 13: desactivar impide nuevas operaciones, pero un cliente
        // inactivo con deuda debe seguir siendo cobrable.
        $clientes = Cliente::where('saldo', '>', 0)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'identificacion', 'saldo']);

        if ($request->filled('venta')) {
            $venta = Venta::with(['cliente', 'abonos.user'])->findOrFail($request->input('venta'));

            // Regla 8: solo se precargan ventas fiadas con saldo pendiente.
            if ($venta->esFiada() && $venta->saldoPendiente() > 0) {
                $ventaSeleccionada = $venta;
                $clienteSeleccionado = $venta->cliente;
            } else {
                $advertencia = __('La venta seleccionada no es fiada o no tiene saldo pendiente.');
            }
        }

        if ($ventaSeleccionada === null && $request->filled('cliente')) {
            $clienteSeleccionado = Cliente::findOrFail($request->input('cliente'));

            // Flujo CU-07: ventas fiadas con saldo pendiente del cliente.
            $ventasCliente = Venta::withSum('abonos as abonado', 'monto')
                ->where('cliente_id', $clienteSeleccionado->id)
                ->where('tipo_venta', Venta::TIPO_FIADO)
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->get()
                ->filter(fn (Venta $venta) => round((float) $venta->total - (float) $venta->abonado, 2) > 0)
                ->values();
        }

        return view('abonos.create', [
            'ventaSeleccionada' => $ventaSeleccionada,
            'clienteSeleccionado' => $clienteSeleccionado,
            'ventasCliente' => $ventasCliente,
            'clientes' => $clientes,
            'advertencia' => $advertencia,
        ]);
    }

    /**
     * Registra el abono sobre una venta fiada (RF-19, reglas 8, 9 y 10).
     */
    public function store(StoreAbonoRequest $request): RedirectResponse
    {
        $monto = round((float) $request->monto, 2);

        // Regla 2: tras redondear a 2 decimales el monto debe seguir siendo > 0.
        if ($monto <= 0) {
            throw ValidationException::withMessages([
                'monto' => 'El monto debe ser mayor que cero.',
            ]);
        }

        $venta = DB::transaction(function () use ($request, $monto) {
            // Bloquea la venta para validar las reglas 8 y 9 sin carreras.
            $venta = Venta::lockForUpdate()->findOrFail($request->venta_id);

            // Regla 8: solo las ventas fiadas admiten abonos.
            if (! $venta->esFiada()) {
                throw ValidationException::withMessages([
                    'venta_id' => 'Solo se pueden registrar abonos sobre ventas fiadas.',
                ]);
            }

            // Regla 8: la venta debe tener saldo pendiente.
            if ($venta->saldoPendiente() <= 0) {
                throw ValidationException::withMessages([
                    'venta_id' => 'La venta seleccionada no tiene saldo pendiente.',
                ]);
            }

            // Regla 9: el monto no puede superar el saldo pendiente de la venta,
            // considerando los abonos ya registrados.
            if ($monto > $venta->saldoPendiente()) {
                $saldo = number_format($venta->saldoPendiente(), 2, '.', '');

                throw ValidationException::withMessages([
                    'monto' => "El monto supera el saldo pendiente de la venta ({$saldo}).",
                ]);
            }

            // Bloquea la fila del cliente: todo escritor de clientes.saldo usa
            // lockForUpdate para evitar lost updates entre abonos y ventas fiadas.
            $cliente = Cliente::lockForUpdate()->findOrFail($venta->cliente_id);

            Abono::create([
                'venta_id' => $venta->id,
                'user_id' => $request->user()->id,
                'fecha' => $request->fecha,
                'monto' => $monto,
                'metodo_pago' => $request->metodo_pago,
                'observacion' => $request->observacion,
            ]);

            // Regla 10: el abono disminuye el saldo del cliente.
            $cliente->saldo = round((float) $cliente->saldo - $monto, 2);
            $cliente->save();

            // Regla 9: recalcula el estado (pendiente → parcial → pagada).
            $venta->actualizarEstadoPago();

            return $venta;
        });

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Abono registrado correctamente.');
    }
}
