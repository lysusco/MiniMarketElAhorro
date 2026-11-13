<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientePortalTest extends TestCase
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

    public function test_el_cliente_accede_a_su_panel(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/panel')->assertOk();
    }

    public function test_el_cliente_accede_al_catalogo(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/catalogo')->assertOk();
    }

    public function test_el_cliente_accede_a_sus_ventas(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/ventas')->assertOk();
    }

    public function test_el_cliente_accede_a_sus_abonos(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/cliente/abonos')->assertOk();
    }

    public function test_el_cliente_no_accede_al_panel_administrativo(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
        $this->actingAs($user)->get('/categorias')->assertForbidden();
    }

    public function test_el_admin_no_accede_al_portal_cliente(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'activo' => true]);

        $this->actingAs($admin)->get('/cliente/panel')->assertForbidden();
    }

    public function test_el_empleado_no_accede_al_portal_cliente(): void
    {
        $empleado = User::factory()->create(['role' => User::ROLE_EMPLEADO, 'activo' => true]);

        $this->actingAs($empleado)->get('/cliente/panel')->assertForbidden();
    }

    public function test_la_raiz_redirige_al_cliente_a_su_panel(): void
    {
        [$user] = $this->clienteConCuenta();

        $this->actingAs($user)->get('/')->assertRedirect(route('cliente.panel'));
    }

    public function test_el_cliente_solo_ve_sus_propias_ventas(): void
    {
        [$userA, $clienteA] = $this->clienteConCuenta();
        [, $clienteB] = $this->clienteConCuenta();

        $ventaA = Venta::create([
            'cliente_id' => $clienteA->id,
            'user_id' => $userA->id,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'contado',
            'subtotal' => 1000,
            'descuento' => 0,
            'total' => 1000,
            'estado' => 'pagada',
        ]);

        $ventaB = Venta::create([
            'cliente_id' => $clienteB->id,
            'user_id' => $userA->id,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'contado',
            'subtotal' => 2000,
            'descuento' => 0,
            'total' => 2000,
            'estado' => 'pagada',
        ]);

        $response = $this->actingAs($userA)->get('/cliente/ventas');

        $response->assertOk();
        $response->assertSee('#' . $ventaA->id);
        $response->assertDontSee('#' . $ventaB->id);
    }

    public function test_el_cliente_no_puede_ver_la_venta_de_otro(): void
    {
        [$userA] = $this->clienteConCuenta();
        [, $clienteB] = $this->clienteConCuenta();

        $ventaB = Venta::create([
            'cliente_id' => $clienteB->id,
            'user_id' => $userA->id,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'contado',
            'subtotal' => 2000,
            'descuento' => 0,
            'total' => 2000,
            'estado' => 'pagada',
        ]);

        $this->actingAs($userA)->get("/cliente/ventas/{$ventaB->id}")->assertNotFound();
    }
}
