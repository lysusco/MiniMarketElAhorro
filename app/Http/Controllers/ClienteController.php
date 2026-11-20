<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    /**
     * Listado de clientes con búsqueda por nombre o identificación.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');

        $clientes = Cliente::query()
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('identificacion', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Formulario de creación.
     */
    public function create(): View
    {
        return view('clientes.create');
    }

    /**
     * Registra un nuevo cliente.
     *
     * Solo el administrador puede definir el límite de crédito;
     * si lo envía un empleado, se ignora y queda en 0.
     */
    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['limite_credito'] = $request->user()->isAdmin()
            ? ($data['limite_credito'] ?? 0)
            : 0;

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualiza un cliente.
     *
     * Solo el administrador puede modificar el límite de crédito;
     * si lo envía un empleado, se ignora y se conserva el valor anterior.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            // El empleado no puede modificar el límite de crédito: se conserva el valor anterior.
            unset($data['limite_credito']);
        } elseif (array_key_exists('limite_credito', $data)) {
            // Un límite vacío equivale a 0 (sin crédito).
            $data['limite_credito'] = $data['limite_credito'] ?? 0;
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Activa o desactiva un cliente.
     *
     * Regla de negocio 13: los clientes no se eliminan físicamente;
     * se desactivan para impedir nuevas operaciones.
     */
    public function toggleActivo(Cliente $cliente): RedirectResponse
    {
        $cliente->update(['activo' => ! $cliente->activo]);

        return redirect()->route('clientes.index')
            ->with('success', $cliente->activo ? 'Cliente activado correctamente.' : 'Cliente desactivado correctamente.');
    }
}
