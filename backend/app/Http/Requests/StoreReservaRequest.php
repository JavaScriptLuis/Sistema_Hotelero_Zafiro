<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueReservation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_inicio' => ['required'],
            'hora_inicio' => ['required'],
            // 'hora_fin' => ['required', 'after:hora_inicio'],
            'hora_fin' => ['required'],
            'observacion' => 'nullable|string',
            'adelanto' => 'nullable|numeric',
            'habitacion_id' => [
                'required',
                'exists:habitacions,id',
                new UniqueReservation($this->habitacion_id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El campo cliente es obligatorio',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'habitacion_id.required' => 'El campo habitación es obligatorio',
            'habitacion_id.exists' => 'La habitación seleccionada no existe',
            'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio',
            'fecha_fin.required' => 'El campo fecha de fin es obligatorio',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'hora_inicio.required' => 'El campo hora de inicio es obligatorio',
            'hora_fin.required' => 'El campo hora de fin es obligatorio',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio',
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
                'fecha_fin' => $errors->first('fecha_fin'),
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