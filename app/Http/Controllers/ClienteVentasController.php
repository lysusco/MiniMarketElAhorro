<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteVentasController extends Controller
{
    public function index(Request $request): View
    {
        $cliente = $this->cliente($request);

        $ventas = $cliente->ventas()
            ->with(['detalles.producto', 'abonos'])
            ->latest('id')
            ->paginate(10);

        return view('cliente.ventas.index', compact('cliente', 'ventas'));
    }

    public function show(Request $request, Venta $venta): View
    {
        $cliente = $this->cliente($request);

        if ($venta->cliente_id !== $cliente->id) {
            abort(404);
        }

        $venta->load(['detalles.producto', 'abonos', 'user']);

        return view('cliente.ventas.show', compact('cliente', 'venta'));
    }

    private function cliente(Request $request): Cliente
    {
        $cliente = $request->user()->cliente;

        if (! $cliente) {
            abort(403, 'Tu cuenta no está vinculada a un cliente.');
        }

        return $cliente;
    }
}
