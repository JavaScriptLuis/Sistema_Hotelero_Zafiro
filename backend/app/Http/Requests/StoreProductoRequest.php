<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sucursal_id' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'descripcion.required' => 'El campo descripción es obligatorio',
            'precio_venta.required' => 'El campo precio de venta es obligatorio',
            'precio_venta.numeric' => 'El precio de venta debe ser un número',
            'precio_venta.min' => 'El precio de venta no puede ser negativo',
            'precio_compra.required' => 'El campo precio de compra es obligatorio',
            'precio_compra.numeric' => 'El precio de compra debe ser un número',
            'precio_compra.min' => 'El precio de compra no puede ser negativo',
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