<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(): View
    {
        $proveedores = Proveedor::when(request('buscar'), function ($query, $buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%")
                    ->orWhere('correo', 'like', "%{$buscar}%");
            });
        })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create(): View
    {
        return view('proveedores.create');
    }

    public function store(StoreProveedorRequest $request): RedirectResponse
    {
        Proveedor::create($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        if ($proveedor->compras()->exists()) {
            $proveedor->update(['activo' => false]);

            return redirect()->route('proveedores.index')
                ->with('success', 'El proveedor tiene compras asociadas, por lo que se desactivó en lugar de eliminarse.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}