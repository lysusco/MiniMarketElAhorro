<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     * (La pluralización en inglés de Laravel no produce "proveedores").
     *
     * @var string
     */
    protected $table = 'proveedores';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'nit',
        'telefono',
        'correo',
        'direccion',
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
            'activo' => 'boolean',
        ];
    }

    /**
     * Compras asociadas al proveedor.
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }
}
