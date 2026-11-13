<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'fecha' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.costo_unitario' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
            'items.required' => 'Debe agregar al menos un producto.',
            'items.array' => 'Los productos no son válidos.',
            'items.min' => 'Debe agregar al menos un producto.',
            'items.*.producto_id.required' => 'El producto es obligatorio.',
            'items.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor que cero.',
            'items.*.costo_unitario.required' => 'El costo unitario es obligatorio.',
            'items.*.costo_unitario.numeric' => 'El costo unitario debe ser un número.',
            'items.*.costo_unitario.gt' => 'El costo unitario debe ser mayor que cero.',
        ];
    }
}