<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'identificacion' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clientes', 'identificacion')->ignore($this->cliente),
            ],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'limite_credito' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'identificacion.required' => 'La identificación del cliente es obligatoria.',
            'identificacion.string' => 'La identificación debe ser un texto.',
            'identificacion.max' => 'La identificación no puede superar los 30 caracteres.',
            'identificacion.unique' => 'Ya existe un cliente con esa identificación.',
            'telefono.string' => 'El teléfono debe ser un texto.',
            'telefono.max' => 'El teléfono no puede superar los 30 caracteres.',
            'correo.email' => 'El correo no es una dirección de correo válida.',
            'correo.max' => 'El correo no puede superar los 100 caracteres.',
            'direccion.string' => 'La dirección debe ser un texto.',
            'direccion.max' => 'La dirección no puede superar los 150 caracteres.',
            'limite_credito.numeric' => 'El límite de crédito debe ser un número.',
            'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
        ];
    }
}
