<?php

namespace App\Http\Requests\CompanyRequest;

use App\Http\Requests\StoreRequest;

class StoreCompanyRequest extends StoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cambia esto si necesitas autorización específica
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'ruc' => 'required|string|max:11',
            'business_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15', // Limitar a un tamaño más razonable para números de teléfono
            'email' => 'nullable|email|max:255', // Validar que sea un correo válido
            'status' => 'required|boolean', // Asegurarse de que sea true o false
            'route' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validar archivo de imagen
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo "nombre" es obligatorio.',
            'name.string' => 'El campo "nombre" debe ser una cadena de texto.',
            'name.max' => 'El campo "nombre" no puede tener más de 255 caracteres.',

            'ruc.required' => 'El campo "RUC" es obligatorio.',
            'ruc.string' => 'El campo "RUC" debe ser una cadena de texto.',
            'ruc.max' => 'El campo "RUC" no puede tener más de 11 caracteres.',

            'business_name.required' => 'El campo "razón social" es obligatorio.',
            'business_name.string' => 'El campo "razón social" debe ser una cadena de texto.',
            'business_name.max' => 'El campo "razón social" no puede tener más de 255 caracteres.',

            'address.string' => 'El campo "dirección" debe ser una cadena de texto.',
            'address.max' => 'El campo "dirección" no puede tener más de 255 caracteres.',

            'phone.string' => 'El campo "teléfono" debe ser una cadena de texto.',
            'phone.max' => 'El campo "teléfono" no puede tener más de 15 caracteres.',

            'email.email' => 'El campo "correo electrónico" debe ser una dirección de correo válida.',
            'email.max' => 'El campo "correo electrónico" no puede tener más de 255 caracteres.',

            'status.required' => 'El campo "estado" es obligatorio.',
            'status.boolean' => 'El campo "estado" debe ser verdadero o falso.',

            'route.image' => 'El archivo debe ser una imagen.',
            'route.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max' => 'El archivo no puede ser mayor a 2 MB.',

        ];
    }

}
