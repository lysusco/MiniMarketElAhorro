<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Task10Pagination403DarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginacion_usa_tema_oscuro(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);

        for ($i = 0; $i < 11; $i++) {
            Categoria::create(['nombre' => "Cat $i"]);
        }

        $this->actingAs($admin)
            ->get('/categorias')
            ->assertOk()
            ->assertSee('bg-bg-200')
            ->assertSee('bg-bg-300')
            ->assertSee('text-text-200')
            ->assertSee('border-bg-300');
    }

    public function test_403_usa_tema_oscuro(): void
    {
        $empleado = User::factory()->create([
            'role' => User::ROLE_EMPLEADO,
            'activo' => true,
        ]);

        $this->actingAs($empleado)
            ->get('/categorias')
            ->assertForbidden()
            ->assertSee('bg-bg-100');
    }
}
