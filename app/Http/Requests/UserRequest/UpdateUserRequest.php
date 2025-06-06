<?php
namespace App\Http\Requests\UserRequest;

use App\Http\Requests\UpdateRequest;
use App\Models\Person;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends UpdateRequest
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

    public function rules()
    {
        $id       = $this->route('id'); // ID del usuario desde la ruta

        return [
            'username'        => [
                'nullable',
                'string',
                Rule::unique('users')->whereNull('deleted_at')->ignore($id), // Ignora el ID del usuario en la ruta
            ],
            'password'        => 'nullable', // El campo de la contraseña es opcional para la actualización

            'type_document'   => 'nullable|string|max:255',
            'type_person'     => 'nullable|string|max:255',

            'names'           => 'nullable|string|max:255',
            'father_surname'  => 'nullable|string|max:255',
            'mother_surname'  => 'nullable|string|max:255',
            'business_name'   => 'nullable|string|max:255',

            'address'         => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:15|regex:/^\+?[0-9\s\-]+$/',
            'email'           => 'nullable|string|max:255|email',
            'rol_id'          => 'nullable|integer|exists:rols,id,deleted_at,NULL',
            'date_birth'      => 'nullable|date',
            'number_document' => [
                'nullable',
                function ($attribute, $value, $fail) use ($id) {
                    // Verificar si el número de documento ya está asociado a una persona existente
                    $person = Person::where('number_document', $value)
                        ->whereNull('deleted_at')
                        ->first();

                },
            ],
        ];
    }

/**
 * Obtén los mensajes personalizados para las reglas de validación.
 */
    public function messages()
    {
        return [
            'email.required'           => 'El correo electrónico es obligatorio.',
            'email.email'              => 'Por favor, ingresa un correo electrónico válido.',
            'email.string'             => 'El correo electrónico debe ser un texto válido.',
            'email.unique'             => 'Este correo electrónico ya está registrado en el sistema.',
            'password.required'        => 'La contraseña es obligatoria.',
            'names.string'             => 'El campo "Nombres" debe ser un texto válido.',
            'names.max'                => 'El campo "Nombres" no debe exceder los 255 caracteres.',
            'father_surname.string'    => 'El campo "Apellido Paterno" debe ser un texto válido.',
            'father_surname.max'       => 'El campo "Apellido Paterno" no debe exceder los 255 caracteres.',
            'mother_surname.string'    => 'El campo "Apellido Materno" debe ser un texto válido.',
            'mother_surname.max'       => 'El campo "Apellido Materno" no debe exceder los 255 caracteres.',
            'address.string'           => 'El campo "Dirección" debe ser un texto válido.',
            'address.max'              => 'El campo "Dirección" no debe exceder los 255 caracteres.',
            'phone.string'             => 'El campo "Teléfono" debe ser un texto válido.',
            'phone.max'                => 'El campo "Teléfono" no debe exceder los 15 caracteres.',
            'phone.regex'              => 'El campo "Teléfono" debe contener solo números, espacios, guiones o un prefijo "+" válido.',
            'number_document.required' => 'El número de documento es obligatorio.',
            'number_document.exists'   => 'El número de documento proporcionado no existe o está inactivo.',
            'username.unique'          => 'El nombre de usuario ya ha sido registrado.', // Aquí

            'rol_id.required'          => 'El Rol es obligatoria.',
            'rol_id.integer'           => 'El identificador del rol debe ser un número entero.',
            'rol_id.exists'            => 'El Rol seleccionado no existe.',
        ];
    }

}
