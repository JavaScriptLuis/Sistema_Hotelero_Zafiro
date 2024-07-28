<?php

namespace App\Http\Requests;

use App\Models\Personal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
            'email.exists' => 'Este correo electrónico no está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = User::where('email', $this->email)->first();
            if (!$user) {
                return $validator->errors()->add('email', 'Este correo electrónico no está registrado.');
            }

            if (!Hash::check($this->password, $user->password)) {
                return $validator->errors()->add('password', 'La contraseña es incorrecta.');
            }

            $personal = Personal::where('user_id', $user->id)->first();
            if ($personal && $personal->estado === 0) {
                return $validator->errors()->add('email', 'Usuario no habilitado.');
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'data' => [
                'email' => $validator->errors()->first('email'),
                'password' => $validator->errors()->first('password'),
            ]
        ], 422));
    }
}
