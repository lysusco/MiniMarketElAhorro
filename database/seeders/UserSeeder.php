<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Crea las cuentas iniciales del sistema: un administrador y un empleado.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@minimercado.com',
            'identificacion' => '1000000001',
            'password' => 'admin123',
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);

        User::create([
            'name' => 'Empleado',
            'email' => 'empleado@minimercado.com',
            'identificacion' => '1000000002',
            'password' => 'empleado123',
            'role' => User::ROLE_EMPLEADO,
            'activo' => true,
        ]);
    }
}
