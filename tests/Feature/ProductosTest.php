<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductosTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);
    }

    private function empleado(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_EMPLEADO,
            'activo' => true,
        ]);
    }

    public function test_el_administrador_puede_crear_un_producto(): void
    {
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);

        $this->actingAs($this->admin())
            ->post('/productos', [
                'categoria_id' => $categoria->id,
                'nombre' => 'Arroz 1kg',
                'codigo' => 'PRD-001',
                'precio_compra' => 2000,
                'precio_venta' => 3000,
                'stock_minimo' => 5,
                'activo' => 1,
            ])
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'codigo' => 'PRD-001',
            'stock_actual' => 0,
        ]);
    }

    public function test_el_codigo_es_obligatorio_y_unico(): void
    {
        $admin = $this->admin();
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);

        $this->actingAs($admin)
            ->post('/productos', [
                'categoria_id' => $categoria->id,
                'nombre' => 'Arroz',
                'codigo' => '',
                'precio_compra' => 1000,
                'precio_venta' => 2000,
                'stock_minimo' => 0,
            ])
            ->assertSessionHasErrors('codigo');

        Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Frijol',
            'codigo' => 'PRD-002',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);

        $this->actingAs($admin)
            ->post('/productos', [
                'categoria_id' => $categoria->id,
                'nombre' => 'Lenteja',
                'codigo' => 'PRD-002',
                'precio_compra' => 1000,
                'precio_venta' => 2000,
                'stock_minimo' => 0,
            ])
            ->assertSessionHasErrors('codigo');
    }

    public function test_el_administrador_puede_editar_un_producto(): void
    {
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Arroz',
            'codigo' => 'PRD-003',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);

        $this->actingAs($this->admin())
            ->put("/productos/{$producto->id}", [
                'categoria_id' => $categoria->id,
                'nombre' => 'Arroz premium',
                'codigo' => 'PRD-003',
                'precio_compra' => 1200,
                'precio_venta' => 2500,
                'stock_minimo' => 3,
                'activo' => 1,
            ])
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Arroz premium',
            'stock_minimo' => 3,
        ]);
    }

    public function test_se_elimina_un_producto_sin_operaciones(): void
    {
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Sal',
            'codigo' => 'PRD-004',
            'precio_compra' => 500,
            'precio_venta' => 1000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);

        $this->actingAs($this->admin())
            ->delete("/productos/{$producto->id}")
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_no_se_elimina_un_producto_con_operaciones_y_se_puede_inactivar(): void
    {
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Aceite',
            'codigo' => 'PRD-005',
            'precio_compra' => 5000,
            'precio_venta' => 7000,
            'stock_actual' => 10,
            'stock_minimo' => 2,
        ]);

        $compra = \App\Models\Compra::create([
            'proveedor_id' => \App\Models\Proveedor::create(['nombre' => 'Prov', 'activo' => true])->id,
            'user_id' => $this->admin()->id,
            'fecha' => now(),
            'total' => 5000,
            'estado' => 'recibida',
        ]);
        DetalleCompra::create([
            'compra_id' => $compra->id,
            'producto_id' => $producto->id,
            'cantidad' => 10,
            'costo_unitario' => 5000,
            'subtotal' => 5000,
        ]);

        $this->actingAs($this->admin())
            ->delete("/productos/{$producto->id}")
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'activo' => true,
        ]);

        $this->actingAs($this->admin())
            ->patch("/productos/{$producto->id}/toggle-activo")
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'activo' => false,
        ]);
    }

    public function test_se_puede_buscar_por_nombre_o_codigo(): void
    {
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);
        Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Arroz',
            'codigo' => 'PRD-A',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);
        Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Frijol',
            'codigo' => 'PRD-F',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);

        $this->actingAs($this->admin())
            ->get('/productos?buscar=Arroz')
            ->assertOk()
            ->assertSee('Arroz')
            ->assertDontSee('Frijol');

        $this->actingAs($this->admin())
            ->get('/productos?buscar=PRD-F')
            ->assertOk()
            ->assertSee('Frijol');
    }

    public function test_se_puede_filtrar_por_stock_bajo(): void
    {
        $categoria = Categoria::create(['nombre' => 'Víveres', 'activo' => true]);
        Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Bajo',
            'codigo' => 'PRD-B',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 1,
            'stock_minimo' => 5,
        ]);
        Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Ok',
            'codigo' => 'PRD-O',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 10,
            'stock_minimo' => 5,
        ]);

        $this->actingAs($this->admin())
            ->get('/productos?stock_bajo=1')
            ->assertOk()
            ->assertSee('Bajo')
            ->assertDontSee('Ok');
    }

    public function test_el_empleado_puede_consultar_pero_no_gestionar(): void
    {
        $this->actingAs($this->empleado())
            ->get('/productos')
            ->assertOk();

        $this->actingAs($this->empleado())
            ->get('/productos/create')
            ->assertForbidden();
    }
}