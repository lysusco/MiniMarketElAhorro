<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nombre',
        'identificacion',
        'telefono',
        'correo',
        'direccion',
        'limite_credito',
        'saldo',
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
            'limite_credito' => 'decimal:2',
            'saldo' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    /**
     * Cuenta de usuario asociada (cuando el cliente inicia sesión).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Crédito disponible para nuevas ventas fiadas (regla de negocio 6).
     */
    public function creditoDisponible(): float
    {
        return (float) $this->limite_credito - (float) $this->saldo;
    }
}
