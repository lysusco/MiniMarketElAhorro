<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientesTest extends TestCase
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

    public function test_admin_puede_crear_cliente_con_limite_de_credito(): void
    {
        $this->actingAs($this->admin())
            ->post('/clientes', [
                'nombre' => 'Doña Rosa',
                'identificacion' => 'CC-100',
                'telefono' => '300',
                'limite_credito' => 50000,
            ])
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'identificacion' => 'CC-100',
            'limite_credito' => 50000,
            'activo' => true,
        ]);
    }

    public function test_empleado_puede_crear_clientes(): void
    {
        $this->actingAs($this->empleado())
            ->post('/clientes', [
                'nombre' => 'Don Pedro',
                'identificacion' => 'CC-200',
                'telefono' => '301',
                'limite_credito' => 999999,
            ])
            ->assertRedirect(route('clientes.index'));

        // El límite de crédito enviado por el empleado se ignora: queda en 0.
        $this->assertDatabaseHas('clientes', [
            'identificacion' => 'CC-200',
            'limite_credito' => 0,
        ]);
    }

    public function test_empleado_no_puede_definir_ni_modificar_el_limite_de_credito(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Doña Rosa',
            'identificacion' => 'CC-100',
            'limite_credito' => 50000,
        ]);

        $this->actingAs($this->empleado())
            ->put("/clientes/{$cliente->id}", [
                'nombre' => 'Doña Rosa',
                'identificacion' => 'CC-100',
                'limite_credito' => 1,
            ])
            ->assertRedirect(route('clientes.index'));

        // El límite de crédito enviado por el empleado se ignora: conserva el anterior.
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'limite_credito' => 50000,
        ]);
    }

    public function test_la_identificacion_del_cliente_es_unica(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/clientes', [
                'nombre' => 'Doña Rosa',
                'identificacion' => 'CC-100',
            ])
            ->assertRedirect(route('clientes.index'));

        $this->actingAs($admin)
            ->post('/clientes', [
                'nombre' => 'Don Pedro',
                'identificacion' => 'CC-100',
            ])
            ->assertSessionHasErrors('identificacion');
    }

    public function test_el_limite_de_credito_no_puede_ser_negativo(): void
    {
        $this->actingAs($this->admin())
            ->post('/clientes', [
                'nombre' => 'Doña Rosa',
                'identificacion' => 'CC-100',
                'limite_credito' => -1,
            ])
            ->assertSessionHasErrors('limite_credito');
    }

    public function test_se_puede_desactivar_y_reactivar_un_cliente(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Doña Rosa',
            'identificacion' => 'CC-100',
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch("/clientes/{$cliente->id}/toggle-activo")
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'activo' => false]);

        $this->actingAs($admin)
            ->patch("/clientes/{$cliente->id}/toggle-activo")
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'activo' => true]);
    }

    public function test_el_cliente_requiere_nombre(): void
    {
        $this->actingAs($this->admin())
            ->post('/clientes', [
                'identificacion' => 'CC-100',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_el_limite_de_credito_vacio_se_guarda_como_cero(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Doña Rosa',
            'identificacion' => 'CC-100',
            'limite_credito' => 50000,
        ]);

        // El formulario envía cadena vacía cuando el campo se deja en blanco.
        $this->actingAs($this->admin())
            ->put("/clientes/{$cliente->id}", [
                'nombre' => 'Doña Rosa',
                'identificacion' => 'CC-100',
                'limite_credito' => '',
            ])
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'limite_credito' => 0,
        ]);
    }
}
