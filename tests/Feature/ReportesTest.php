<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesTest extends TestCase
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

    /**
     * Creación directa de una venta con los modelos (los reportes son de
     * solo lectura y no dependen del registro de ventas).
     */
    private function ventaDe(User $usuario, array $atributos = []): Venta
    {
        return Venta::create(array_merge([
            'user_id' => $usuario->id,
            'fecha' => now()->toDateString(),
            'tipo_venta' => Venta::TIPO_CONTADO,
            'subtotal' => 0,
            'descuento' => 0,
            'total' => 0,
            'estado' => Venta::ESTADO_PAGADA,
        ], $atributos));
    }

    private function cliente(array $atributos = []): Cliente
    {
        return Cliente::create(array_merge([
            'nombre' => 'Cliente de prueba',
            'identificacion' => 'CC-R-'.uniqid(),
            'activo' => true,
        ], $atributos));
    }

    public function test_el_admin_ve_el_reporte_de_ventas_por_fecha(): void
    {
        $admin = $this->admin();
        $ayer = now()->subDay()->toDateString();
        $hoy = now()->toDateString();

        $ventaAnterior = $this->ventaDe($admin, ['fecha' => $ayer, 'subtotal' => 900, 'total' => 900]);
        $ventaHoy = $this->ventaDe($admin, ['fecha' => $hoy, 'subtotal' => 1500, 'total' => 1500]);

        $this->actingAs($admin)
            ->get("/reportes/ventas?fecha_desde={$hoy}&fecha_hasta={$hoy}")
            ->assertOk()
            ->assertSee('Venta #')
            ->assertSee('Venta #'.$ventaHoy->id)
            ->assertDontSee('Venta #'.$ventaAnterior->id)
            ->assertSee('1,500');

        $this->actingAs($admin)
            ->get('/reportes/ventas')
            ->assertOk()
            ->assertSee('Venta #'.$ventaHoy->id)
            ->assertSee('Venta #'.$ventaAnterior->id);
    }

    public function test_el_reporte_de_cartera_lista_solo_clientes_con_saldo_pendiente(): void
    {
        $this->cliente(['nombre' => 'Cliente Con Deuda', 'limite_credito' => 8000, 'saldo' => 5000]);
        $this->cliente(['nombre' => 'Cliente Sin Deuda', 'saldo' => 0]);

        $this->actingAs($this->admin())
            ->get('/reportes/cartera')
            ->assertOk()
            ->assertSee('5,000')
            ->assertDontSee('Cliente Sin Deuda');
    }

    public function test_cartera_muestra_el_credito_disponible(): void
    {
        $this->cliente(['nombre' => 'Cliente Con Credito', 'limite_credito' => 10000, 'saldo' => 4000]);

        $this->actingAs($this->admin())
            ->get('/reportes/cartera')
            ->assertOk()
            ->assertSee('6,000');
    }

    public function test_el_empleado_no_puede_ver_los_reportes(): void
    {
        $empleado = $this->empleado();

        $this->actingAs($empleado)->get('/reportes/ventas')->assertStatus(403);
        $this->actingAs($empleado)->get('/reportes/cartera')->assertStatus(403);
    }

    public function test_el_reporte_de_ventas_muestra_los_totales(): void
    {
        $admin = $this->admin();
        $hoy = now()->toDateString();

        $this->ventaDe($admin, [
            'fecha' => $hoy,
            'tipo_venta' => Venta::TIPO_CONTADO,
            'subtotal' => 3000,
            'total' => 3000,
        ]);

        $this->ventaDe($admin, [
            'cliente_id' => $this->cliente(['limite_credito' => 10000, 'saldo' => 0])->id,
            'fecha' => $hoy,
            'tipo_venta' => Venta::TIPO_FIADO,
            'subtotal' => 2000,
            'total' => 2000,
            'estado' => Venta::ESTADO_PENDIENTE,
        ]);

        $this->actingAs($admin)
            ->get('/reportes/ventas')
            ->assertOk()
            ->assertSee('5,000');
    }
}
