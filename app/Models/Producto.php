<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'categoria_id',
        'nombre',
        'codigo',
        'precio_compra',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'activo',
    ];

    /**
     * Obtiene los atributos que deben convertirse de tipo.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'stock_actual' => 'integer',
            'stock_minimo' => 'integer',
            'activo' => 'boolean',
        ];
    }

    /**
     * Categoría a la que pertenece el producto.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Detalles de compra donde aparece el producto.
     */
    public function detalleCompras(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Detalles de venta donde aparece el producto.
     */
    public function detalleVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Movimientos de inventario del producto.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Indica si el producto está en stock igual o menor al mínimo (RF-11).
     */
    public function enStockBajo(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}
