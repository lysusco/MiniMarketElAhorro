<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Muestra el listado de categorías con búsqueda.
     */
    public function index(): View
    {
        $categorias = Categoria::withCount('productos')
            ->when(request('buscar'), function ($query, $buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario para crear una categoría.
     */
    public function create(): View
    {
        return view('categorias.create');
    }

    /**
     * Registra una nueva categoría.
     */
    public function store(StoreCategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Muestra el formulario para editar una categoría.
     */
    public function edit(Categoria $categoria): View
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualiza una categoría.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Elimina o desactiva una categoría.
     *
     * Regla de negocio 13: si la categoría tiene productos relacionados,
     * no se elimina físicamente; se desactiva para impedir nuevas operaciones.
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->productos()->exists()) {
            $categoria->update(['activo' => false]);

            return redirect()->route('categorias.index')
                ->with('success', 'La categoría tiene productos relacionados, por lo que se desactivó en lugar de eliminarse.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
