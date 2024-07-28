<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTipoHabitacionRequest extends FormRequest
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
            'nombre' => 'required|unique:tipo_habitacions,nombre',
            'descripcion' => 'required',
            'precio_fijo' => 'required|numeric',
            'precio_decremento' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.unique' => 'El nombre del tipo de habitación ya existe',
            'descripcion.required' => 'El campo descripción es obligatorio',
            'precio_fijo.required' => 'El campo precio fijo es obligatorio',
            'precio_fijo.numeric' => 'El campo precio fijo debe ser un número',
            'precio_decremento.required' => 'El campo precio decremento es obligatorio',
            'precio_decremento.numeric' => 'El campo precio decremento debe ser un número',
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
                'descripcion' => $errors->first('descripcion'),
                'precio_fijo' => $errors->first('precio_fijo'),
                'precio_decremento' => $errors->first('precio_decremento'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
