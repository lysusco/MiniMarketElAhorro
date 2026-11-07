<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'venta_id',
        'user_id',
        'fecha',
        'monto',
        'metodo_pago',
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
            'fecha' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    /**
     * Venta fiada sobre la que se registra el abono.
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Usuario que registró el abono.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
