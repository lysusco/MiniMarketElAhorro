<?php

namespace Tests\Feature;

use App\Models\Abono;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentasTest extends TestCase
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

    private function productoConStock(int $stock): Producto
    {
        return Producto::create([
            'categoria_id' => Categoria::create(['nombre' => 'Cat', 'activo' => true])->id,
            'nombre' => 'Arroz',
            'codigo' => 'PRD-V-'.uniqid(),
            'precio_compra' => 1000,
            'precio_venta' => 2000,
            'stock_actual' => $stock,
            'stock_minimo' => 0,
        ]);
    }

    private function cliente(array $atributos = []): Cliente
    {
        return Cliente::create(array_merge([
            'nombre' => 'Cliente de prueba',
            'identificacion' => 'CC-V-'.uniqid(),
            'activo' => true,
        ], $atributos));
    }

    public function test_se_puede_registrar_una_venta_de_contado(): void
    {
        $admin = $this->admin();
        $producto = $this->productoConStock(10);

        $this->actingAs($admin)
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 3],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        $this->assertDatabaseHas('ventas', ['tipo_venta' => 'contado', 'estado' => 'pagada', 'total' => 6000, 'subtotal' => 6000, 'cliente_id' => null]);
        $this->assertDatabaseHas('detalle_ventas', ['cantidad' => 3, 'precio_unitario' => 2000, 'subtotal' => 6000]);
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock_actual' => 7]);
        $this->assertDatabaseHas('movimientos_inventario', ['producto_id' => $producto->id, 'tipo' => 'salida', 'cantidad' => 3]);
    }

    public function test_se_rechaza_la_venta_si_no_hay_stock_suficiente(): void
    {
        $producto = $this->productoConStock(2);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 5],
                ],
            ])
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_la_venta_fiada_aumenta_el_saldo_del_cliente(): void
    {
        $cliente = $this->cliente(['limite_credito' => 10000, 'saldo' => 0]);
        $producto = $this->productoConStock(10);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'fiado',
                'cliente_id' => $cliente->id,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 2],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'saldo' => 4000]);
        $this->assertDatabaseHas('ventas', ['estado' => 'pendiente']);
    }

    public function test_se_rechaza_la_venta_fiada_si_supera_el_limite_de_credito(): void
    {
        $cliente = $this->cliente(['limite_credito' => 10000, 'saldo' => 8000]);
        $producto = $this->productoConStock(10);
        $producto->update(['precio_venta' => 3000]);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'fiado',
                'cliente_id' => $cliente->id,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertSessionHasErrors('cliente_id');

        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'saldo' => 8000]);
    }

    public function test_la_venta_fiada_en_el_limite_exacto_es_permitida(): void
    {
        $cliente = $this->cliente(['limite_credito' => 10000, 'saldo' => 8000]);
        $producto = $this->productoConStock(10);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'fiado',
                'cliente_id' => $cliente->id,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        $this->assertDatabaseHas('ventas', ['estado' => 'pendiente']);
        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'saldo' => 10000]);
    }

    public function test_la_venta_fiada_requiere_cliente_activo(): void
    {
        $inactivo = $this->cliente(['activo' => false, 'limite_credito' => 10000]);
        $producto = $this->productoConStock(10);

        // Cliente inactivo.
        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'fiado',
                'cliente_id' => $inactivo->id,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertSessionHasErrors('cliente_id');

        // Fiado sin cliente_id.
        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'fiado',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertSessionHasErrors('cliente_id');

        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_el_precio_unitario_conserva_el_precio_historico(): void
    {
        $producto = $this->productoConStock(10);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        \App\Models\Producto::find($producto->id)->update(['precio_venta' => 9999]);

        $this->assertDatabaseHas('detalle_ventas', ['precio_unitario' => 2000]);
    }

    public function test_las_cantidades_duplicadas_del_mismo_producto_se_suman(): void
    {
        $producto = $this->productoConStock(5);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 2],
                    ['producto_id' => $producto->id, 'cantidad' => 3],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        $this->assertDatabaseCount('ventas', 1);
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'stock_actual' => 0]);
    }

    public function test_no_se_puede_vender_un_producto_inactivo(): void
    {
        $producto = $this->productoConStock(10);
        $producto->update(['activo' => false]);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertSessionHasErrors('items');

        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_el_descuento_no_puede_superar_el_subtotal(): void
    {
        $producto = $this->productoConStock(10);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'descuento' => 5000,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertSessionHasErrors('descuento');
    }

    public function test_el_empleado_puede_registrar_ventas(): void
    {
        $this->actingAs($this->empleado())
            ->get('/ventas/create')
            ->assertOk();
    }

    public function test_actualizar_estado_pago_recalcula_segun_los_abonos(): void
    {
        $admin = $this->admin();
        $cliente = $this->cliente(['limite_credito' => 10000, 'saldo' => 0]);
        $producto = $this->productoConStock(10);

        $this->actingAs($admin)
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'fiado',
                'cliente_id' => $cliente->id,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 2],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        $venta = Venta::first();
        $this->assertEquals(Venta::ESTADO_PENDIENTE, $venta->estado);

        // Abono parcial: el estado pasa a parcial.
        Abono::create([
            'venta_id' => $venta->id,
            'user_id' => $admin->id,
            'fecha' => now()->toDateString(),
            'monto' => 1000,
            'metodo_pago' => 'efectivo',
        ]);
        $venta->actualizarEstadoPago();
        $this->assertEquals(Venta::ESTADO_PARCIAL, $venta->fresh()->estado);

        // Abono que cubre el saldo pendiente: el estado pasa a pagada.
        Abono::create([
            'venta_id' => $venta->id,
            'user_id' => $admin->id,
            'fecha' => now()->toDateString(),
            'monto' => 3000,
            'metodo_pago' => 'efectivo',
        ]);
        $venta->actualizarEstadoPago();
        $this->assertEquals(Venta::ESTADO_PAGADA, $venta->fresh()->estado);
    }
}
