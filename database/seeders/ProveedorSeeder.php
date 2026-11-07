<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Crea los proveedores iniciales del minimercado.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Distribuidora La Sabana',
                'telefono' => '607 651 2345',
                'correo' => 'ventas@distritolasa.com',
                'direccion' => 'Cra. 18 # 32-45, Bucaramanga',
            ],
            [
                'nombre' => 'Alimentos del Oriente',
                'telefono' => '607 697 8890',
                'correo' => 'pedidos@alimentosdeloriente.com',
                'direccion' => 'Cl. 20 # 12-08, Floridablanca',
            ],
            [
                'nombre' => 'Aseo Total S.A.S.',
                'telefono' => '607 643 1122',
                'correo' => 'comercial@aseototal.com',
                'direccion' => 'Aut. Girón km 4, Girón',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}
