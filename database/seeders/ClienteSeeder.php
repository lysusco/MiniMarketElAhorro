<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Crea los clientes iniciales con su límite de crédito y una cuenta
     * de usuario con rol cliente para que puedan consultar su información.
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

        foreach ($clientes as $datos) {
            $correo = $datos['correo'] ?: strtolower(str_replace(' ', '.', $datos['nombre'])) . '@cliente.minimercado.com';

            $usuario = User::create([
                'name' => $datos['nombre'],
                'email' => $correo,
                'password' => 'cliente123',
                'role' => User::ROLE_CLIENTE,
                'activo' => true,
            ]);

            Cliente::create($datos + [
                'user_id' => $usuario->id,
                'saldo' => 0,
                'activo' => true,
            ]);
        }
    }
}
