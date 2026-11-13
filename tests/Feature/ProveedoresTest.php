<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedoresTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'activo' => true,
        ]);
    }

    public function test_el_administrador_puede_crear_un_proveedor(): void
    {
        $this->actingAs($this->admin())
            ->post('/proveedores', [
                'nombre' => 'Distribuidora XYZ',
                'telefono' => '3001234567',
                'correo' => 'ventas@xyz.com',
                'direccion' => 'Calle 1 #2-3',
                'activo' => 1,
            ])
            ->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', ['nombre' => 'Distribuidora XYZ']);
    }

    public function test_el_nombre_es_obligatorio(): void
    {
        $this->actingAs($this->admin())
            ->post('/proveedores', ['nombre' => ''])
            ->assertSessionHasErrors('nombre');
    }

    public function test_el_administrador_puede_editar_un_proveedor(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor A', 'activo' => true]);

        $this->actingAs($this->admin())
            ->put("/proveedores/{$proveedor->id}", [
                'nombre' => 'Proveedor A Editado',
            ])
            ->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'nombre' => 'Proveedor A Editado',
        ]);
    }

    public function test_se_elimina_un_proveedor_sin_compras(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor B', 'activo' => true]);

        $this->actingAs($this->admin())
            ->delete("/proveedores/{$proveedor->id}")
            ->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseMissing('proveedores', ['id' => $proveedor->id]);
    }

    public function test_se_desactiva_un_proveedor_con_compras(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor C', 'activo' => true]);
        \App\Models\Compra::create([
            'proveedor_id' => $proveedor->id,
            'user_id' => $this->admin()->id,
            'fecha' => now(),
            'total' => 1000,
            'estado' => 'recibida',
        ]);

        $this->actingAs($this->admin())
            ->delete("/proveedores/{$proveedor->id}")
            ->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'activo' => false,
        ]);
    }

    public function test_se_puede_buscar_por_nombre(): void
    {
        Proveedor::create(['nombre' => 'Alfa']);
        Proveedor::create(['nombre' => 'Beta']);

        $this->actingAs($this->admin())
            ->get('/proveedores?buscar=Beta')
            ->assertOk()
            ->assertSee('Beta')
            ->assertDontSee('Alfa');
    }

    public function test_no_se_puede_crear_proveedor_con_nit_duplicado(): void
    {
        Proveedor::create(['nombre' => 'Prov A', 'nit' => '900111111-1', 'activo' => true]);

        $this->actingAs($this->admin())
            ->post('/proveedores', [
                'nombre' => 'Prov B',
                'nit' => '900111111-1',
            ])
            ->assertSessionHasErrors('nit');
    }

    public function test_el_empleado_no_puede_gestionar_proveedores(): void
    {
        $empleado = User::factory()->create([
            'role' => User::ROLE_EMPLEADO,
            'activo' => true,
        ]);

        $this->actingAs($empleado)
            ->get('/proveedores')
            ->assertForbidden();
    }
}