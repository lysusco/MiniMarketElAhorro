<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $cliente = $this->cliente($request);

        $ventas = $cliente->ventas()->latest('id')->take(5)->get();
        $abonos = Abono::whereHas('venta', fn ($q) => $q->where('cliente_id', $cliente->id))
            ->with('venta')
            ->latest('id')
            ->take(5)
            ->get();

        return view('cliente.panel', [
            'cliente' => $cliente,
            'ventasRecientes' => $ventas,
            'abonosRecientes' => $abonos,
        ]);
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
