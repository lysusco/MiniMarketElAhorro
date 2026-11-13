<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriasDarkTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);
    }

    public function test_categorias_index_usa_tema_oscuro(): void
    {
        $this->actingAs($this->admin())
            ->get('/categorias')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }

    public function test_categorias_create_usa_tema_oscuro(): void
    {
        $this->actingAs($this->admin())
            ->get('/categorias/create')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }

    public function test_categorias_edit_usa_tema_oscuro(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas']);

        $this->actingAs($this->admin())
            ->get("/categorias/{$categoria->id}/edit")
            ->assertOk()
            ->assertSee('bg-bg-200');
    }
}
