<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'nit' => ['nullable', 'string', 'max:20', 'unique:proveedores,nit'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'nit.string' => 'El NIT debe ser un texto.',
            'nit.max' => 'El NIT no puede superar los 20 caracteres.',
            'nit.unique' => 'Ya existe un proveedor con ese NIT.',
            'telefono.string' => 'El teléfono debe ser un texto.',
            'telefono.max' => 'El teléfono no puede superar los 20 caracteres.',
            'correo.email' => 'El correo no es válido.',
            'correo.max' => 'El correo no puede superar los 100 caracteres.',
            'direccion.string' => 'La dirección debe ser un texto.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',
            'activo.boolean' => 'El estado activo no es válido.',
        ];
    }
}