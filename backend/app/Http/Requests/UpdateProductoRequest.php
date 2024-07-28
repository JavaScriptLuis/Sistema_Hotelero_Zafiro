<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductoRequest extends FormRequest
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
            'sucursal_id' => 'sometimes|required',
            'nombre' => 'sometimes|required',
            'descripcion' => 'sometimes|required',
            'precio_venta' => 'sometimes|required|numeric',
            'precio_compra' => 'sometimes|required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'Debe seleccionar una sucursal',
            'nombre.required' => 'El campo nombre es obligatorio',
            'descripcion.required' => 'El campo descripcion es obligatorio',
            'precio_venta.required' => 'El campo precio venta es obligatorio',
            'precio_venta.numeric' => 'El campo precio venta debe ser un número',
            'precio_compra.required' => 'El campo precio compra es obligatorio',
            'precio_compra.numeric' => 'El campo precio compra debe ser un número',
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
                'descripcion' => $errors->first('descripcion'),
                'precio_venta' => $errors->first('precio_venta'),
                'precio_compra' => $errors->first('precio_compra'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}