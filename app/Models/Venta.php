<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'tipo_venta',
        'subtotal',
        'descuento',
        'total',
        'estado',
    ];

    /**
     * Obtiene los atributos que deben convertirse de tipo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Cliente de la venta (opcional en ventas de contado).
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Usuario que registró la venta.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Productos incluidos en la venta.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Abonos recibidos sobre la venta (solo ventas fiadas).
     */
    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    /**
     * Movimientos de inventario originados por la venta.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Indica si la venta es fiada.
     */
    public function esFiada(): bool
    {
        return $this->tipo_venta === 'fiado';
    }

    /**
     * Saldo pendiente de la venta: total menos los abonos registrados (regla 9).
     */
    public function saldoPendiente(): float
    {
        return (float) $this->total - (float) $this->abonos()->sum('monto');
    }
}
