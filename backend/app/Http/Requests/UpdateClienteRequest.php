<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
         'nombre' => 'required',
         'paterno' => 'required',
         'materno' => 'required',
         'cedula' => 'required',
         'telefono' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'paterno.required' => 'El campo apellido paterno es obligatorio',
            'materno.required' => 'El campo apellido materno es obligatorio',
            'cedula.required' => 'La campo cédula es obligatoria',
            'telefono.required' => 'El campo teléfono es obligatorio',
        ];
    }

}
