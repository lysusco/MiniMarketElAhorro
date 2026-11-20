<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'tipo_venta' => ['required', 'in:contado,fiado'],
            'cliente_id' => ['required_if:tipo_venta,fiado', 'nullable', 'integer', 'exists:clientes,id'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
            'tipo_venta.required' => 'El tipo de venta es obligatorio.',
            'tipo_venta.in' => 'El tipo de venta debe ser contado o fiado.',
            'cliente_id.required_if' => 'El cliente es obligatorio en las ventas fiadas.',
            'cliente_id.integer' => 'El cliente seleccionado no es válido.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'descuento.numeric' => 'El descuento debe ser un número.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'items.required' => 'Debe agregar al menos un producto.',
            'items.array' => 'Los productos no son válidos.',
            'items.min' => 'Debe agregar al menos un producto.',
            'items.*.producto_id.required' => 'El producto es obligatorio.',
            'items.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor que cero.',
        ];
    }
}
