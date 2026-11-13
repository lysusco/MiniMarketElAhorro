<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivo' => ['required', 'string', 'in:daño,vencimiento,pérdida,otro'],
            'observacion' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'El motivo es obligatorio.',
            'motivo.in' => 'El motivo no es válido.',
            'observacion.string' => 'La observación debe ser un texto.',
            'observacion.max' => 'La observación no puede superar los 255 caracteres.',
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