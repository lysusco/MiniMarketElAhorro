<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteCatalogoController extends Controller
{
    public function index(Request $request): View
    {
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->string('buscar');
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('codigo', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('categoria'), function ($query) use ($request) {
                $query->whereHas('categoria', fn ($q) => $q->where('nombre', $request->string('categoria')));
            })
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        $categorias = \App\Models\Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('cliente.catalogo.index', compact('productos', 'categorias'));
    }
}
