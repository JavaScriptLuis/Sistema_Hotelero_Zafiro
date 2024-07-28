<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Registro;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckSalidaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajusta esto según tus necesidades de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'total_pago' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'total_pago.required' => 'El total de pago es requerido',
            'total_pago.numeric' => 'El total de pago debe ser un número',
            'total_pago.min' => 'El total de pago no puede ser negativo',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $estadia = Registro::find($this->route('id'));

            if (!$estadia) {
                $validator->errors()->add('id', 'Estadia no encontrada');
                return;
            }

            $consumo = $estadia->consumo;
            if ($consumo) {
                $detallesPendientes = $consumo->detalles()->where('estado', 'pendiente')->count();
                
                if ($detallesPendientes > 0) {
                    $validator->errors()->add('consumo', 'Hay detalles de consumo pendientes de pago');
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'total_pago' => $errors->first('total_pago'),
                'id' => $errors->first('id'),
                'consumo' => $errors->first('consumo'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}