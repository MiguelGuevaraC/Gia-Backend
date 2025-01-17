<?php

namespace App\Http\Requests\PersonRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\UpdateRequest;
class UpdatePersonRequest extends UpdateRequest
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
    public function rules($id)
    {
        $id = $this->route('id'); // Obtén el ID de la ruta, que se asume que es el ID del usuario
    
        return [
            'type_document' => 'required|string|max:50',
            'type_person' => 'required|string|in:NATURAL,JURIDICA', // Opciones válidas
            'number_document' => "required|string|max:20|unique:people,number_document,{$id},id,deleted_at,NULL",
            'names' => 'required|string|max:255',
            'father_surname' => 'nullable|string|max:255',
            'mother_surname' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|regex:/^\d{9,15}$/', // Acepta números de 9 a 15 dígitos
            'email' => "nullable|email|max:255|unique:people,email,{$id},id,deleted_at,NULL",
            'ocupation' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'server_id' => 'nullable|integer|exists:servers,id', // Cambiar 'servers' según tu tabla
        ];
    }

    public function messages()
{
    return [
        'type_document.required' => 'El tipo de documento es obligatorio.',
        'type_document.string' => 'El tipo de documento debe ser una cadena de texto.',
        'type_document.max' => 'El tipo de documento no debe exceder los 50 caracteres.',

        'type_person.required' => 'El tipo de persona es obligatorio.',
        'type_person.string' => 'El tipo de persona debe ser una cadena de texto.',
        'type_person.in' => 'El tipo de persona debe ser "NATURAL" o "JURIDICA".',

        'number_document.required' => 'El número de documento es obligatorio.',
        'number_document.string' => 'El número de documento debe ser una cadena de texto.',
        'number_document.max' => 'El número de documento no debe exceder los 20 caracteres.',
        'number_document.unique' => 'El número de documento ya está registrado.',

        'names.required' => 'El nombre es obligatorio.',
        'names.string' => 'El nombre debe ser una cadena de texto.',
        'names.max' => 'El nombre no debe exceder los 255 caracteres.',

        'father_surname.string' => 'El apellido paterno debe ser una cadena de texto.',
        'father_surname.max' => 'El apellido paterno no debe exceder los 255 caracteres.',

        'mother_surname.string' => 'El apellido materno debe ser una cadena de texto.',
        'mother_surname.max' => 'El apellido materno no debe exceder los 255 caracteres.',

        'business_name.string' => 'La razón social debe ser una cadena de texto.',
        'business_name.max' => 'La razón social no debe exceder los 255 caracteres.',

        'address.string' => 'La dirección debe ser una cadena de texto.',
        'address.max' => 'La dirección no debe exceder los 255 caracteres.',

        'phone.string' => 'El teléfono debe ser una cadena de texto.',
        'phone.regex' => 'El teléfono debe contener entre 9 y 15 dígitos.',

        'email.email' => 'El correo electrónico debe ser una dirección válida.',
        'email.max' => 'El correo electrónico no debe exceder los 255 caracteres.',
        'email.unique' => 'El correo electrónico ya está registrado.',

        'occupation.string' => 'La ocupación debe ser una cadena de texto.',
        'occupation.max' => 'La ocupación no debe exceder los 255 caracteres.',

        'status.required' => 'El estado es obligatorio.',
        'status.string' => 'El estado debe ser una cadena.',

        'server_id.integer' => 'El servidor debe ser un número entero.',
        'server_id.exists' => 'El servidor seleccionado no existe.',
    ];
}
    

}
