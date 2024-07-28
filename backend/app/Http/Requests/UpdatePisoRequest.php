<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdatePisoRequest extends FormRequest
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
            'sucursal_id' => 'required',
            'nombre' => 'required|unique:pisos,nombre',
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'El campo sucursal es obligatorio',
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.unique' => 'El nombre del piso ya existe',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'sucursal_id' => $errors->first('sucursal_id'),
                'nombre' => $errors->first('nombre'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
