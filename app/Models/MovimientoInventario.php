<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'movimientos_inventario';

    /**
     * Tipos de movimiento de inventario.
     * entrada: suma stock (compra).
     * salida: descuenta stock (venta).
     * ajuste: descuenta stock (daño, vencimiento o pérdida).
     */
    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SALIDA = 'salida';
    public const TIPO_AJUSTE = 'ajuste';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'producto_id',
        'user_id',
        'compra_id',
        'venta_id',
        'tipo',
        'cantidad',
        'fecha',
        'observacion',
    ];

    /**
     * Obtiene los atributos que deben convertirse de tipo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'fecha' => 'date',
        ];
    }

    /**
     * Producto del movimiento.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Usuario responsable del movimiento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Compra que originó el movimiento (si aplica).
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Venta que originó el movimiento (si aplica).
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}
