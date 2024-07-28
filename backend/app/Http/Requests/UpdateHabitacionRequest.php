<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHabitacionRequest extends FormRequest
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
            'piso_id' => 'required',
            'numero' => 'required',
            'descripcion' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'piso_id.required' => 'El campo piso es obligatorio',
            'numero.required' => 'El campo número es obligatorio',
            'numero.unique' => 'El número de habitación ya existe',
            'descripcion.required' => 'El campo descripción es obligatorio',
        ];
    }
}
