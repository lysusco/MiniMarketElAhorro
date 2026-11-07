<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Crea el catálogo inicial de productos.
     *
     * El stock inicial se deja en 0: las existencias solo cambian mediante
     * entradas, ventas o ajustes (decisión de diseño del documento).
     * El flujo demo (DatosDemoSeeder) registra la compra inicial que surte
     * el inventario y deja el historial de movimientos completo.
     */
    public function run(): void
    {
        $productos = [
            // Víveres
            ['categoria' => 'Víveres', 'nombre' => 'Arroz Diana 500 g', 'codigo' => 'PRD-001', 'precio_compra' => 3200, 'precio_venta' => 4500, 'stock_minimo' => 10],
            ['categoria' => 'Víveres', 'nombre' => 'Aceite Girasol 1 L', 'codigo' => 'PRD-002', 'precio_compra' => 9500, 'precio_venta' => 12000, 'stock_minimo' => 6],
            ['categoria' => 'Víveres', 'nombre' => 'Huevos AA x 30', 'codigo' => 'PRD-003', 'precio_compra' => 14500, 'precio_venta' => 18000, 'stock_minimo' => 5],
            ['categoria' => 'Víveres', 'nombre' => 'Leche Entera 1 L', 'codigo' => 'PRD-004', 'precio_compra' => 3100, 'precio_venta' => 4200, 'stock_minimo' => 12],
            ['categoria' => 'Víveres', 'nombre' => 'Azúcar Manuelita 1 kg', 'codigo' => 'PRD-005', 'precio_compra' => 3600, 'precio_venta' => 4800, 'stock_minimo' => 8],
            // Aseo
            ['categoria' => 'Aseo', 'nombre' => 'Detergente Fab 500 g', 'codigo' => 'PRD-006', 'precio_compra' => 4900, 'precio_venta' => 6500, 'stock_minimo' => 8],
            ['categoria' => 'Aseo', 'nombre' => 'Jabón Rey x 3', 'codigo' => 'PRD-007', 'precio_compra' => 5900, 'precio_venta' => 7800, 'stock_minimo' => 6],
            ['categoria' => 'Aseo', 'nombre' => 'Papel Higiénico Familia x 4', 'codigo' => 'PRD-008', 'precio_compra' => 7200, 'precio_venta' => 9500, 'stock_minimo' => 6],
            // Bebidas
            ['categoria' => 'Bebidas', 'nombre' => 'Gaseosa Coca-Cola 1.5 L', 'codigo' => 'PRD-009', 'precio_compra' => 5100, 'precio_venta' => 6800, 'stock_minimo' => 10],
            ['categoria' => 'Bebidas', 'nombre' => 'Agua Cristal 600 ml', 'codigo' => 'PRD-010', 'precio_compra' => 1500, 'precio_venta' => 2500, 'stock_minimo' => 12],
            ['categoria' => 'Bebidas', 'nombre' => 'Jugo Hit 1.5 L', 'codigo' => 'PRD-011', 'precio_compra' => 3900, 'precio_venta' => 5200, 'stock_minimo' => 8],
            // Snacks
            ['categoria' => 'Snacks', 'nombre' => 'Papas Margarita 150 g', 'codigo' => 'PRD-012', 'precio_compra' => 3300, 'precio_venta' => 4500, 'stock_minimo' => 10],
            ['categoria' => 'Snacks', 'nombre' => 'Galletas Noel x 6', 'codigo' => 'PRD-013', 'precio_compra' => 2800, 'precio_venta' => 3800, 'stock_minimo' => 10],
            // Canasta familiar
            ['categoria' => 'Canasta familiar', 'nombre' => 'Pan Tajado Bimbo', 'codigo' => 'PRD-014', 'precio_compra' => 4800, 'precio_venta' => 6500, 'stock_minimo' => 5],
            ['categoria' => 'Canasta familiar', 'nombre' => 'Café Sello Rojo 250 g', 'codigo' => 'PRD-015', 'precio_compra' => 7600, 'precio_venta' => 9800, 'stock_minimo' => 6],
        ];

        foreach ($productos as $producto) {
            $categoria = Categoria::where('nombre', $producto['categoria'])->firstOrFail();

            Producto::create([
                'categoria_id' => $categoria->id,
                'nombre' => $producto['nombre'],
                'codigo' => $producto['codigo'],
                'precio_compra' => $producto['precio_compra'],
                'precio_venta' => $producto['precio_venta'],
                'stock_actual' => 0,
                'stock_minimo' => $producto['stock_minimo'],
                'activo' => true,
            ]);
        }
    }
}
