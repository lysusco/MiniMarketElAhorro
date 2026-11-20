<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbonosTest extends TestCase
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
            'codigo' => 'PRD-A-'.uniqid(),
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
            'identificacion' => 'CC-A-'.uniqid(),
            'activo' => true,
        ], $atributos));
    }

    /**
     * Crea una venta fiada con el total indicado usando el flujo real de
     * VentaController: el saldo del cliente queda en $saldoCliente + $total.
     */
    private function ventaFiada(float $total, float $saldoCliente = 0): Venta
    {
        $cliente = $this->cliente([
            'limite_credito' => 1000000,
            'saldo' => $saldoCliente,
        ]);
        $producto = $this->productoConStock(10);
        $producto->update(['precio_venta' => $total]);

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

        return Venta::firstOrFail();
    }

    public function test_se_puede_registrar_un_abono_en_una_venta_fiada(): void
    {
        $venta = $this->ventaFiada(10000);

        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 4000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('abonos', ['monto' => 4000, 'metodo_pago' => 'efectivo']);
        $this->assertDatabaseHas('ventas', ['id' => $venta->id, 'estado' => 'parcial']);
        $this->assertDatabaseHas('clientes', ['id' => $venta->cliente_id, 'saldo' => 6000]);
    }

    public function test_el_abono_total_marca_la_venta_como_pagada(): void
    {
        $venta = $this->ventaFiada(10000);

        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 10000,
                'metodo_pago' => 'transferencia',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ventas', ['estado' => 'pagada']);
        $this->assertDatabaseHas('clientes', ['saldo' => 0]);
    }

    public function test_no_se_puede_abonar_mas_del_saldo_pendiente(): void
    {
        $venta = $this->ventaFiada(10000);

        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 11000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('monto');

        $this->assertDatabaseCount('abonos', 0);
        // El cliente queda sin cambios.
        $this->assertDatabaseHas('clientes', ['id' => $venta->cliente_id, 'saldo' => 10000]);
    }

    public function test_no_se_puede_abonar_una_venta_de_contado(): void
    {
        $cliente = $this->cliente(['limite_credito' => 10000, 'saldo' => 0]);
        $producto = $this->productoConStock(10);

        $this->actingAs($this->admin())
            ->post('/ventas', [
                'fecha' => now()->toDateString(),
                'tipo_venta' => 'contado',
                'cliente_id' => $cliente->id,
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 1],
                ],
            ])
            ->assertRedirect(route('ventas.index'));

        $venta = Venta::firstOrFail();

        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 2000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('venta_id');

        $this->assertDatabaseCount('abonos', 0);
    }

    public function test_no_se_puede_abonar_una_venta_ya_pagada(): void
    {
        $venta = $this->ventaFiada(10000);

        // Abono previo que salda la venta.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 10000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertRedirect();

        // La venta ya no tiene saldo pendiente.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 1000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('venta_id');
    }

    public function test_el_monto_del_abono_debe_ser_mayor_que_cero(): void
    {
        $venta = $this->ventaFiada(10000);

        // Monto en cero.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 0,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('monto');

        // Monto negativo.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => -500,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('monto');

        // Monto que al redondear a 2 decimales queda en cero.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 0.004,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('monto');

        $this->assertDatabaseCount('abonos', 0);
    }

    public function test_abonos_acumulados_actualizan_saldo_y_estado(): void
    {
        $venta = $this->ventaFiada(10000);

        // Primer abono parcial.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 6000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ventas', ['id' => $venta->id, 'estado' => 'parcial']);
        $this->assertDatabaseHas('clientes', ['id' => $venta->cliente_id, 'saldo' => 4000]);

        // Segundo abono que cubre el resto.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 4000,
                'metodo_pago' => 'transferencia',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ventas', ['id' => $venta->id, 'estado' => 'pagada']);
        $this->assertDatabaseHas('clientes', ['id' => $venta->cliente_id, 'saldo' => 0]);
    }

    public function test_no_se_puede_abonar_mas_del_saldo_pendiente_restante(): void
    {
        $venta = $this->ventaFiada(10000);

        // Abono parcial válido.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 6000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertRedirect();

        // El saldo restante es 4000: 5000 lo supera.
        $this->actingAs($this->admin())
            ->post('/abonos', [
                'venta_id' => $venta->id,
                'fecha' => now()->toDateString(),
                'monto' => 5000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertSessionHasErrors('monto');

        $this->assertDatabaseCount('abonos', 1);
        $this->assertDatabaseHas('clientes', ['id' => $venta->cliente_id, 'saldo' => 4000]);
    }

    public function test_el_empleado_puede_registrar_abonos(): void
    {
        $this->actingAs($this->empleado())
            ->get('/abonos/create')
            ->assertOk();
    }

    public function test_el_cliente_no_puede_registrar_abonos(): void
    {
        $clienteUser = User::factory()->create([
            'role' => User::ROLE_CLIENTE,
            'activo' => true,
        ]);

        $this->actingAs($clienteUser)
            ->get('/abonos/create')
            ->assertForbidden();

        $this->actingAs($clienteUser)
            ->post('/abonos', [
                'venta_id' => 1,
                'fecha' => now()->toDateString(),
                'monto' => 1000,
                'metodo_pago' => 'efectivo',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('abonos', 0);
    }
}
