<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Crea los clientes iniciales con su límite de crédito.
     * El saldo inicia en 0: aumenta con las ventas fiadas y disminuye
     * con los abonos (decisión de diseño del documento).
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'María Fernanda López',
                'identificacion' => '1.098.765.432',
                'telefono' => '300 456 7890',
                'correo' => 'maria.lopez@correo.com',
                'direccion' => 'Cl. 15 # 9-20, Bucaramanga',
                'limite_credito' => 300000,
            ],
            [
                'nombre' => 'Carlos Andrés Gómez',
                'identificacion' => '80.123.456',
                'telefono' => '315 123 4567',
                'correo' => 'carlos.gomez@correo.com',
                'direccion' => 'Cra. 7 # 41-15, Floridablanca',
                'limite_credito' => 500000,
            ],
            [
                'nombre' => 'Rosa Elena Pinto',
                'identificacion' => '45.678.901',
                'telefono' => '320 987 6543',
                'correo' => null,
                'direccion' => 'Cl. 30 # 18-02, Bucaramanga',
                'limite_credito' => 200000,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente + ['saldo' => 0, 'activo' => true]);
        }
    }
}
