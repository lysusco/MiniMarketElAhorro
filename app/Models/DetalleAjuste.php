<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleAjuste extends Model
{
    use HasFactory;

    protected $table = 'detalle_ajustes';

    protected $fillable = [
        'ajuste_id',
        'producto_id',
        'cantidad',
    ];

    public function ajuste(): BelongsTo
    {
        return $this->belongsTo(Ajuste::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}