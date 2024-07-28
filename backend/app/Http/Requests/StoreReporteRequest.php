<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreReporteRequest extends FormRequest
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
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'personal_id' => 'required|exists:personals,id',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.required' => 'El campo fecha inicio es obligatorio',
            'fecha_fin.required' => 'El campo fecha fin es obligatorio',
            'personal_id.required' => 'El campo personal es obligatorio',
            'personal_id.exists' => 'El personal no existe',
            'fecha_fin.after_or_equal' => 'La fecha fin debe ser mayor o igual a la fecha inicio',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'fecha_inicio' => $errors->first('fecha_inicio'),
                'fecha_fin' => $errors->first('fecha_fin'),
                'personal_id' => $errors->first('personal_id'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
