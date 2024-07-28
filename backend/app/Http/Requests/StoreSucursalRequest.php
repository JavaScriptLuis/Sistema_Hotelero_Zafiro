<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSucursalRequest extends FormRequest
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
            'nombre_sucursal' => 'required|unique:sucursals,nombre_sucursal',
            'direccion' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_sucursal.required' => 'El campo nombre de la sucursal es obligatorio',
            'direccion.required' => 'El campo dirección es obligatorio',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'nombre_sucursal' => $errors->first('nombre_sucursal'),
                'direccion' => $errors->first('direccion'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
