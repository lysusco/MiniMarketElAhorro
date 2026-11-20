<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbonoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'venta_id' => ['required', 'integer', 'exists:ventas,id'],
            'fecha' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'gt:0'],
            'metodo_pago' => ['required', 'in:efectivo,transferencia,otro'],
            'observacion' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'venta_id.required' => 'La venta es obligatoria.',
            'venta_id.integer' => 'La venta seleccionada no es válida.',
            'venta_id.exists' => 'La venta seleccionada no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.gt' => 'El monto debe ser mayor que cero.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'El método de pago debe ser efectivo, transferencia u otro.',
            'observacion.string' => 'La observación no es válida.',
            'observacion.max' => 'La observación no puede superar los 200 caracteres.',
        ];
    }
}
