<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StorePersonalRequest extends FormRequest
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
            'sucursal_id' => 'required',
            'email' => 'required|unique:users,email',
            'nombre' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'cedula' => 'required|unique:personals,cedula',
            'telefono' => 'required',
            'rol' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'Debe seleccionar una sucursal',
            'cedula.unique' => 'La cedula de identidad ya ha sido registrada',
            'nombre.required' => 'El campo nombre es obligatorio',
            'paterno.required' => 'El campo apellido paterno es obligatorio',
            'materno.required' => 'El campo apellido materno es obligatorio',
            'cedula.required' => 'La campo cédula es obligatoria',
            'telefono.required' => 'El campo teléfono es obligatorio',
            'email.required' => 'El campo email es obligatorio',
            'rol.required' => 'El campo rol es obligatorio',
            'email.unique' => 'El email ya se encuentra registrado en el sistema',
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
                'email' => $errors->first('email'),
                'cedula' => $errors->first('cedula'),
                'nombre' => $errors->first('nombre'),
                'paterno' => $errors->first('paterno'),
                'materno' => $errors->first('materno'),
                'telefono' => $errors->first('telefono'),
                'rol' => $errors->first('rol'),
            ]
        ], 422);

        throw new HttpResponseException($response);
    }
}
