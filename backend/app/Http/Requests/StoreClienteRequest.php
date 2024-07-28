<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClienteRequest extends FormRequest
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
         'cedula' => 'required|numeric|unique:clientes,cedula',
        ];
    }

    public function messages()
    {
        return [
            'cedula.unique' => 'La cedula de identidad ya ha sido registrada',
            'nombre.required' => 'El campo nombre es obligatorio',
            'paterno.required' => 'El campo apellido paterno es obligatorio',
            'materno.required' => 'El campo apellido materno es obligatorio',
            'cedula.required' => 'La campo cédula es obligatoria',
            'cedula.numeric' => 'La cédula debe ser numérica',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'nombre' => $errors->first('nombre'),
                'paterno' => $errors->first('paterno'),
                'materno' => $errors->first('materno'),
                'cedula' => $errors->first('cedula'),
                'telefono' => $errors->first('telefono'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
