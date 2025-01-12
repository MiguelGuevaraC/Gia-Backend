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
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255', // Opcional, pero si está presente debe cumplir con las reglas
            'type' => 'sometimes|string|max:255', // Opcional y limitado a 50 caracteres
            'status' => 'sometimes|boolean', // Opcional, pero si está presente debe ser booleano
            'route' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048', // Opcional, archivo debe ser imagen
            'environment_id' => 'sometimes|integer|exists:environments,id', // Opcional, pero debe existir en la tabla 'environments'
        ];
    }
    

    public function messages()
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
    
            'type.string' => 'El tipo debe ser una cadena de texto.',
            'type.max' => 'El tipo no puede tener más de 50 caracteres.',
    
            'status.boolean' => 'El estado debe ser verdadero o falso.',
    
            'route.image' => 'El archivo debe ser una imagen.',
            'route.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max' => 'El archivo no puede ser mayor a 2 MB.',
    
            'environment_id.integer' => 'El identificador del ambiente debe ser un número entero.',
            'environment_id.exists' => 'El ambiente seleccionado no existe.',
        ];
    }
    

}
