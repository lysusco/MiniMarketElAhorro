<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprasTest extends TestCase
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

    public function test_se_puede_registrar_una_entrada(): void
    {
        $admin = $this->admin();
        $proveedor = Proveedor::create(['nombre' => 'Prov', 'activo' => true]);
        $producto = Producto::create([
            'categoria_id' => \App\Models\Categoria::create(['nombre' => 'Cat', 'activo' => true])->id,
            'nombre' => 'Arroz',
            'codigo' => 'PRD-1',
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => 0,
            'stock_minimo' => 0,
        ]);

        $response = $this->actingAs($admin)
            ->post('/compras', [
                'proveedor_id' => $proveedor->id,
                'fecha' => now()->toDateString(),
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 5, 'costo_unitario' => 1200],
                ],
            ])
            ->assertRedirect(route('compras.index'));

        $compra = \App\Models\Compra::first();
        $this->assertEquals(\App\Models\Compra::ESTADO_EN_PROGRESO, $compra->estado);

        // Stock no cambia hasta finalizar.
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock_actual' => 0]);

        $this->actingAs($admin)
            ->post("/compras/{$compra->id}/finalizar")
            ->assertRedirect(route('compras.index'));

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock_actual' => 5, 'precio_compra' => 1200]);
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
        ]);
        $this->assertDatabaseHas('compras', ['id' => $compra->id, 'estado' => \App\Models\Compra::ESTADO_ACTIVO]);
    }

    public function test_se_requiere_al_menos_un_producto(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Prov', 'activo' => true]);

        $this->actingAs($this->admin())
            ->post('/compras', [
                'proveedor_id' => $proveedor->id,
                'fecha' => now()->toDateString(),
                'items' => [],
            ])
            ->assertSessionHasErrors('items');
    }

    public function test_el_empleado_puede_registrar_entradas(): void
    {
        $this->actingAs($this->empleado())
            ->get('/compras/create')
            ->assertOk();
    }
}