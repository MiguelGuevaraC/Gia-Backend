<?php
namespace App\Http\Requests\UserRequest;

use App\Http\Requests\StoreRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class StoreUserAppRequest extends StoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'          => 'required|email|unique:users,username,NULL,id,deleted_at,NULL',
            'phone'          => 'required|string',
            'token_form'     => 'required|string',
            'password'       => [
                'required',
                'string',
                'min:8',         // Mínimo 8 caracteres
                'regex:/[a-z]/', // Al menos una letra minúscula
                'regex:/[A-Z]/', // Al menos una letra mayúscula
                'regex:/[0-9]/', // Al menos un número
                'regex:/[\W]/',  // Al menos un carácter especial
            ],
            'names'          => 'required|string',
            'father_surname' => 'nullable|string|max:255',
            'mother_surname' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'El correo electrónico debe tener un formato válido.',
            'email.unique'        => 'Este correo electrónico ya está registrado.',
            'phone.required'      => 'El número de teléfono es obligatorio.',
            'phone.string'        => 'El número de teléfono debe ser un texto válido.',
            'token_form.required' => 'El token del formulario es obligatorio.',
            'token_form.string'   => 'El token debe ser una cadena de texto.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.string'     => 'La contraseña debe ser una cadena de texto.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'      => 'La contraseña debe contener al menos una letra minúscula, una letra mayúscula, un número y un carácter especial.',
            'names.required'      => 'El nombre es obligatorio.',
            'names.string'        => 'El nombre debe ser un texto válido.',
            'father_surname.string' => 'El apellido paterno debe ser un texto válido.',
            'father_surname.max'  => 'El apellido paterno no debe exceder los 255 caracteres.',
            'mother_surname.string' => 'El apellido materno debe ser un texto válido.',
            'mother_surname.max'  => 'El apellido materno no debe exceder los 255 caracteres.',
        ];
    }

}
