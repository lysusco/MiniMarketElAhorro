<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ajuste extends Model
{
    use HasFactory;

    public const ESTADO_EN_PROGRESO = 'en_progreso';
    public const ESTADO_INACTIVO = 'inactivo';
    public const ESTADO_ACTIVO = 'activo';

    protected $fillable = [
        'user_id',
        'fecha',
        'motivo',
        'observacion',
        'estado',
        'motivo_inactivacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleAjuste::class);
    }
}