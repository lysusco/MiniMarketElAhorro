<?php

namespace Database\Seeders;

use App\Models\Abono;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosDemoSeeder extends Seeder
{
    /**
     * Registra un flujo completo de operaciones para tener datos de prueba
     * coherentes con las reglas del negocio:
     *
     * 1. Compra inicial a proveedor (entrada que surte el inventario).
     * 2. Venta de contado (salida de stock, pagada al confirmarse).
     * 3. Venta fiada (salida de stock, aumenta el saldo del cliente).
     * 4. Abono sobre la venta fiada (disminuye el saldo del cliente).
     *
     * Cada operación se registra en una transacción y genera sus movimientos
     * de inventario, tal como exige el documento funcional.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@minimercado.com')->firstOrFail();
        $empleado = User::where('email', 'empleado@minimercado.com')->firstOrFail();
        $proveedor = Proveedor::firstOrFail();
        $cliente = Cliente::where('identificacion', '1.098.765.432')->firstOrFail();

        $this->registrarCompraInicial($admin, $proveedor);
        $this->registrarVentaContado($empleado);
        $this->registrarVentaFiadaConAbono($admin, $empleado, $cliente);
    }

    /**
     * Compra inicial: entrada de mercancía que aumenta el stock.
     */
    private function registrarCompraInicial(User $user, Proveedor $proveedor): void
    {
        DB::transaction(function () use ($user, $proveedor) {
            $fecha = now()->subDays(3)->toDateString();

            $compra = Compra::create([
                'proveedor_id' => $proveedor->id,
                'user_id' => $user->id,
                'fecha' => $fecha,
                'total' => 0,
                'estado' => Compra::ESTADO_ACTIVO,
            ]);

            $cantidades = [
                'PRD-001' => 24, 'PRD-002' => 12, 'PRD-003' => 10, 'PRD-004' => 24,
                'PRD-005' => 12, 'PRD-006' => 12, 'PRD-007' => 12, 'PRD-008' => 12,
                'PRD-009' => 18, 'PRD-010' => 24, 'PRD-011' => 12, 'PRD-012' => 18,
                'PRD-013' => 18, 'PRD-014' => 10, 'PRD-015' => 12,
            ];

            $total = 0;

            foreach ($cantidades as $codigo => $cantidad) {
                $producto = Producto::where('codigo', $codigo)->firstOrFail();
                $subtotal = $cantidad * (float) $producto->precio_compra;

                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $producto->precio_compra,
                    'subtotal' => $subtotal,
                ]);

                $producto->stock_actual += $cantidad;
                $producto->save();

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'user_id' => $user->id,
                    'compra_id' => $compra->id,
                    'venta_id' => null,
                    'tipo' => MovimientoInventario::TIPO_ENTRADA,
                    'cantidad' => $cantidad,
                    'fecha' => $fecha,
                    'observacion' => "Entrada por compra #{$compra->id} a {$proveedor->nombre}",
                ]);

                $total += $subtotal;
            }

            $compra->update(['total' => $total]);
        });
    }

    /**
     * Venta de contado: descuenta stock y queda pagada al confirmarse.
     */
    private function registrarVentaContado(User $user): void
    {
        DB::transaction(function () use ($user) {
            $fecha = now()->toDateString();

            $venta = Venta::create([
                'cliente_id' => null,
                'user_id' => $user->id,
                'fecha' => $fecha,
                'tipo_venta' => 'contado',
                'subtotal' => 0,
                'descuento' => 0,
                'total' => 0,
                'estado' => 'pagada',
            ]);

            $cantidades = ['PRD-001' => 2, 'PRD-009' => 1, 'PRD-012' => 3];
            $this->agregarDetallesYStock($venta, $user, $cantidades, $fecha);
        });
    }

    /**
     * Venta fiada con un abono parcial posterior.
     */
    private function registrarVentaFiadaConAbono(User $admin, User $empleado, Cliente $cliente): void
    {
        DB::transaction(function () use ($admin, $empleado, $cliente) {
            $fechaVenta = now()->subDay()->toDateString();

            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'user_id' => $admin->id,
                'fecha' => $fechaVenta,
                'tipo_venta' => 'fiado',
                'subtotal' => 0,
                'descuento' => 0,
                'total' => 0,
                'estado' => 'pendiente',
            ]);

            $cantidades = ['PRD-004' => 4, 'PRD-014' => 2, 'PRD-015' => 1];
            $total = $this->agregarDetallesYStock($venta, $admin, $cantidades, $fechaVenta);

            // Regla 6: la venta fiada aumenta el saldo del cliente.
            $cliente->saldo = (float) $cliente->saldo + $total;
            $cliente->save();

            // Abono parcial registrado hoy: disminuye el saldo del cliente.
            $monto = 20000;

            Abono::create([
                'venta_id' => $venta->id,
                'user_id' => $empleado->id,
                'fecha' => now()->toDateString(),
                'monto' => $monto,
                'metodo_pago' => 'efectivo',
                'observacion' => 'Abono parcial en efectivo',
            ]);

            $cliente->saldo = (float) $cliente->saldo - $monto;
            $cliente->save();

            $venta->update(['estado' => 'parcial']);
        });
    }

    /**
     * Crea los detalles de una venta, descuenta el stock y registra los
     * movimientos de salida. Devuelve el total de la venta.
     *
     * @param  array<string, int>  $cantidades
     */
    private function agregarDetallesYStock(Venta $venta, User $user, array $cantidades, string $fecha): float
    {
        $subtotal = 0;

        foreach ($cantidades as $codigo => $cantidad) {
            $producto = Producto::where('codigo', $codigo)->firstOrFail();

            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $cantidad * (float) $producto->precio_venta,
            ]);

            $producto->stock_actual -= $cantidad;
            $producto->save();

            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'user_id' => $user->id,
                'compra_id' => null,
                'venta_id' => $venta->id,
                'tipo' => MovimientoInventario::TIPO_SALIDA,
                'cantidad' => $cantidad,
                'fecha' => $fecha,
                'observacion' => "Salida por venta #{$venta->id}",
            ]);

            $subtotal += $cantidad * (float) $producto->precio_venta;
        }

        $venta->update(['subtotal' => $subtotal, 'total' => $subtotal]);

        return $subtotal;
    }
}
