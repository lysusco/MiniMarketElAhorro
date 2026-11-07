<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriasTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);
    }

    public function test_el_administrador_puede_crear_una_categoria(): void
    {
        $this->actingAs($this->admin())
            ->post('/categorias', [
                'nombre' => 'Víveres',
                'descripcion' => 'Productos básicos de cocina.',
            ])
            ->assertRedirect(route('categorias.index'));

        $this->assertDatabaseHas('categorias', ['nombre' => 'Víveres']);
    }

    public function test_el_nombre_es_obligatorio_y_unico(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/categorias', ['nombre' => ''])
            ->assertSessionHasErrors('nombre');

        Categoria::create(['nombre' => 'Aseo']);

        $this->actingAs($admin)
            ->post('/categorias', ['nombre' => 'Aseo'])
            ->assertSessionHasErrors('nombre');
    }

    public function test_el_administrador_puede_editar_una_categoria(): void
    {
        $categoria = Categoria::create(['nombre' => 'Bebidas']);

        $this->actingAs($this->admin())
            ->put("/categorias/{$categoria->id}", [
                'nombre' => 'Bebidas y jugos',
            ])
            ->assertRedirect(route('categorias.index'));

        $this->assertDatabaseHas('categorias', [
            'id' => $categoria->id,
            'nombre' => 'Bebidas y jugos',
        ]);
    }

    public function test_se_elimina_una_categoria_sin_productos(): void
    {
        $categoria = Categoria::create(['nombre' => 'Snacks']);

        $this->actingAs($this->admin())
            ->delete("/categorias/{$categoria->id}")
            ->assertRedirect(route('categorias.index'));

        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_se_desactiva_una_categoria_con_productos(): void
    {
        // Regla de negocio 13: los registros con operaciones históricas
        // no se eliminan físicamente; se desactivan.
        $categoria = Categoria::create(['nombre' => 'Aseo', 'activo' => true]);
        Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Detergente',
            'codigo' => 'PRD-999',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);

        $this->actingAs($this->admin())
            ->delete("/categorias/{$categoria->id}")
            ->assertRedirect(route('categorias.index'));

        $this->assertDatabaseHas('categorias', [
            'id' => $categoria->id,
            'activo' => false,
        ]);
    }

    public function test_se_puede_buscar_por_nombre(): void
    {
        Categoria::create(['nombre' => 'Víveres']);
        Categoria::create(['nombre' => 'Aseo']);

        $this->actingAs($this->admin())
            ->get('/categorias?buscar=Aseo')
            ->assertOk()
            ->assertSee('Aseo')
            ->assertDontSee('Víveres');
    }
}
