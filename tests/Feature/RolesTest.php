<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_empleado_no_puede_gestionar_categorias(): void
    {
        // Matriz de permisos: gestionar categorías es solo del administrador.
        $empleado = User::factory()->create([
            'role' => User::ROLE_EMPLEADO,
            'activo' => true,
        ]);

        $this->actingAs($empleado)
            ->get('/categorias')
            ->assertForbidden();

        $this->actingAs($empleado)
            ->post('/categorias', ['nombre' => 'Aseo'])
            ->assertForbidden();
    }

    public function test_el_administrador_puede_gestionar_categorias(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->get('/categorias')
            ->assertOk();
    }

    public function test_un_usuario_inactivo_no_puede_operar(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => false,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertForbidden();
    }
}
