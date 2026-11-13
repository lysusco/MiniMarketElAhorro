<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteDarkTest extends TestCase
{
    use RefreshDatabase;

    private function clienteConCuenta(): array
    {
        $user = User::factory()->cliente()->create();
        $cliente = Cliente::create([
            'user_id' => $user->id,
            'nombre' => 'Cliente Demo',
            'identificacion' => 'CLI-' . uniqid(),
            'limite_credito' => 100000,
            'saldo' => 0,
            'activo' => true,
        ]);

        return [$user, $cliente];
    }

    public function test_cliente_panel_usa_tema_oscuro(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/panel')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }

    public function test_cliente_ventas_index_usa_tema_oscuro(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/ventas')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }

    public function test_cliente_ventas_show_usa_tema_oscuro(): void
    {
        [$user, $cliente] = $this->clienteConCuenta();

        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'user_id' => $user->id,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'contado',
            'subtotal' => 1000,
            'descuento' => 0,
            'total' => 1000,
            'estado' => 'pagada',
        ]);

        $this->actingAs($user)->get("/cliente/ventas/{$venta->id}")
            ->assertOk()
            ->assertSee('bg-bg-200');
    }

    public function test_cliente_abonos_index_usa_tema_oscuro(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/abonos')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }

    public function test_cliente_catalogo_usa_tema_oscuro(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/catalogo')
            ->assertOk()
            ->assertSee('bg-bg-200');
    }
}