<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreConsumoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'registro_id' => 'required|exists:registros,id',
            'total' => 'required|numeric',
            'estado' => 'required|in:pendiente,pagado',
            'productos' => 'array|min:1',
        ];

        if ($this->input('estado') === 'pagado') {
            $rules['metodo_pago'] = 'required|string';
        } else {
            $rules['metodo_pago'] = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'registro_id.required' => 'El ID de registro es requerido',
            'registro_id.exists' => 'El ID de registro no existe',
            'total.required' => 'Debe agregar algun consumo',
            'estado.required' => 'Debe seleccionar la opción pagar',
            'metodo_pago.required' => 'Debe seleccionar el metodo de pago',
            'productos.min' => 'Debe ingresar al menos un producto',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'metodo_pago' => $errors->first('metodo_pago'),
                'estado' => $errors->first('estado'),
                'total' => $errors->first('total'),
                'productos' => $errors->first('productos'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}