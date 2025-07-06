<?php
namespace App\Http\Requests\UserRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Rule;

class UpdateUserPasswordRequest extends StoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'username')->whereNull('deleted_at'),
            ],
            'token_form' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/', // Minúscula
                'regex:/[A-Z]/', // Mayúscula
                'regex:/[0-9]/', // Número
                'regex:/[\W]/',  // Especial
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.exists' => 'Este correo electrónico no está registrado en el sistema.',
            'token_form.required' => 'El token del formulario es obligatorio.',
            'token_form.string' => 'El token debe ser una cadena de texto.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra minúscula, una letra mayúscula, un número y un carácter especial.',
        ];
    }
}
