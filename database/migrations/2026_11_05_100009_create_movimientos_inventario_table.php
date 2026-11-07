<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('user_id')->constrained('users'); // usuario responsable
            // Origen del movimiento: una compra (entrada), una venta (salida) o nulo si es un ajuste manual.
            $table->foreignId('compra_id')->nullable()->constrained('compras')->nullOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            // entrada: suma stock | salida: descuenta stock | ajuste: descuenta stock (daño, vencimiento o pérdida)
            $table->string('tipo');
            $table->unsignedInteger('cantidad');
            $table->date('fecha');
            $table->string('observacion')->nullable(); // motivo u operación de origen
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
