<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreRegistroRequest extends FormRequest
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
            'cliente_id' => 'required',
            'fecha_inicio' => ['required'],
            // 'hora_fin' => ['required', 'after:hora_inicio'],
            'hora_inicio' => ['required'],
            'hora_fin' => ['required'],
            'observacion' => 'nullable|string',
            'adelanto' => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El campo cliente es obligatorio',
            'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio',
            'hora_inicio.required' => 'El campo hora de inicio es obligatorio',
            'hora_fin.required' => 'El campo hora de fin es obligatorio',
            'adelanto.numeric' => 'El campo adelanto debe ser un valor numérico',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'cliente_id' => $errors->first('cliente_id'),
                'fecha_inicio' => $errors->first('fecha_inicio'),
                'hora_inicio' => $errors->first('hora_inicio'),
                'hora_fin' => $errors->first('hora_fin'),
                'observacion' => $errors->first('observacion'),
                'adelanto' => $errors->first('adelanto'),
                'habitacion_id' => $errors->first('habitacion_id'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
