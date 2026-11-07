<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Crea las categorías iniciales de productos.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Víveres', 'descripcion' => 'Granos, aceites, lácteos y productos básicos de cocina.'],
            ['nombre' => 'Aseo', 'descripcion' => 'Detergentes, jabones, papel higiénico y productos de limpieza.'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Gaseosas, aguas, jugos y demás bebidas.'],
            ['nombre' => 'Snacks', 'descripcion' => 'Papas, galletas, dulces y aperitivos.'],
            ['nombre' => 'Canasta familiar', 'descripcion' => 'Pan, café y productos de consumo diario del hogar.'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
