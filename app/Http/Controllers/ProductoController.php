<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Listado de productos con búsqueda y filtros.
     */
    public function index(): View
    {
        $buscar = request('buscar', request('search', request('q')));
        $categoriaId = request('categoria_id', request('categoria'));
        $stockBajo = request()->boolean('stock_bajo');

        $productos = Producto::with('categoria')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigo', 'like', "%{$buscar}%");
                });
            })
            ->when($categoriaId, function ($query, $categoriaId) {
                $query->where('categoria_id', $categoriaId);
            })
            ->when($stockBajo, function ($query) {
                $query->whereColumn('stock_actual', '<=', 'stock_minimo');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * Formulario de creación.
     */
    public function create(): View
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('productos.create', compact('categorias'));
    }

    /**
     * Registra un nuevo producto (stock inicial 0).
     */
    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['stock_actual'] = 0;
        $data['activo'] = $request->boolean('activo', true);

        Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Producto $producto): View
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualiza un producto.
     */
    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina o desactiva un producto.
     *
     * Regla de negocio 13: si el producto tiene operaciones históricas
     * (compras, ventas o movimientos), no se elimina; se ofrece desactivarlo.
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        if ($producto->detalleCompras()->exists()
            || $producto->detalleVentas()->exists()
            || $producto->movimientosInventario()->exists()) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene compras, ventas o movimientos asociados. Puedes inactivarlo en su lugar.');
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Activa o desactiva un producto.
     */
    public function toggleActivo(Producto $producto): RedirectResponse
    {
        $producto->update(['activo' => ! $producto->activo]);

        return redirect()->route('productos.index')
            ->with('success', $producto->activo ? 'Producto activado correctamente.' : 'Producto desactivado correctamente.');
    }
}