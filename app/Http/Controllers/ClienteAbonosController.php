<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteAbonosController extends Controller
{
    public function index(Request $request): View
    {
        $cliente = $this->cliente($request);

        $abonos = Abono::whereHas('venta', fn ($q) => $q->where('cliente_id', $cliente->id))
            ->with(['venta', 'user'])
            ->latest('id')
            ->paginate(10);

        return view('cliente.abonos.index', compact('cliente', 'abonos'));
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
