<?php

namespace App\Http\Requests\EnvironmentRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\UpdateRequest;
class UpdateEnvironmentRequest extends UpdateRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000', // Permitir descripciones más largas
            'route' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validar archivo de imagen
            'status' => 'required|boolean', // Asegurar que sea true o false
            'server_id' => 'nullable|integer', // Validar que sea un entero
            'company_id' => 'required|integer|exists:companies,id,deleted_at,NULL',
        ];
    }
    
    
    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
    
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no puede tener más de 1000 caracteres.',
    
            'route.image' => 'El archivo debe ser una imagen.',
            'route.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max' => 'El archivo no puede ser mayor a 2 MB.',
    
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
    
            'server_id.integer' => 'El identificador del servidor debe ser un número entero.',
    
            'company_id.required' => 'La compañía es obligatoria.',
            'company_id.integer' => 'El identificador de la compañía debe ser un número entero.',
            'company_id.exists' => 'La compañía seleccionada no existe.',
        ];
    }

}
