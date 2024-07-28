<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreHabitacionRequest extends FormRequest
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
            'sucursal_id' => 'required',
            'tipo_id' => 'required',
            'numero' => 'required|unique:habitacions,numero',
            'descripcion' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'piso_id.required' => 'El campo piso es obligatorio',
            'tipo_id.required' => 'El campo tipo de habitación es obligatorio',
            'sucursal_id.required' => 'El campo sucursal es obligatorio',
            'numero.required' => 'El campo número es obligatorio',
            'numero.unique' => 'El número de habitación ya existe',
            'descripcion.required' => 'El campo descripción es obligatorio',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'piso_id' => $errors->first('piso_id'),
                'sucursal_id' => $errors->first('sucursal_id'),
                'numero' => $errors->first('numero'),
                'descripcion' => $errors->first('descripcion'),
                'tipo_id' => $errors->first('tipo_id'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
