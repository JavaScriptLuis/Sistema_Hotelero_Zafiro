<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueReservationUpdate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReservaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $reservaId = $this->route('reserva'); 

        return [
            'cliente_id' => 'sometimes|required|exists:clientes,id',
            'habitacion_id' => [
                'sometimes',
                'required',
                'exists:habitacions,id',
                new UniqueReservationUpdate($this->habitacion_id, $reservaId)
            ],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
            'hora_inicio' => ['sometimes', 'required', 'date_format:H:i'],
            'hora_fin' => ['sometimes', 'required', 'date_format:H:i', 'after:hora_inicio'],
            'observacion' => 'sometimes|nullable|string',
            'adelanto' => 'sometimes|nullable|numeric',
        ];
    }

    public function messages()
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
                'habitacion_id' => $errors->first('habitacion_id'),
                'fecha_inicio' => $errors->first('fecha_inicio'),
                'fecha_fin' => $errors->first('fecha_fin'),
                'hora_inicio' => $errors->first('hora_inicio'),
                'hora_fin' => $errors->first('hora_fin'),
                'observacion' => $errors->first('observacion'),
                'adelanto' => $errors->first('adelanto'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}