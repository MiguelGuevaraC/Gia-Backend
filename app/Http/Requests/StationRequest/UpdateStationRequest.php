<?php

namespace App\Http\Requests\StationRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\UpdateRequest;
use App\Models\Station;
use Illuminate\Contracts\Validation\Validator;

class UpdateStationRequest extends UpdateRequest
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
            'description' => 'sometimes|string|max:300', // Limitar la longitud del tipo
            'price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|numeric|min:0',

               'price_unitario' => 'nullable|numeric|min:0',
            'quantity_people' => 'nullable|numeric|min:0',

           'status' => [
            'sometimes',
            'string',
            function ($attribute, $value, $fail) {
                $id = $this->route('id'); // Obtiene el ID de la estación desde la ruta
                $station = Station::find($id); // Busca la estación en la base de datos
                
                if ($station && $station->status === 'Reservado' && $value !== 'Reservado') {
                    $fail('No se puede cambiar el estado de una estación Reservado a otro estado.');
                }
            },
        ],
            'route' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048', // Opcional, archivo debe ser imagen
            'environment_id' => 'sometimes|integer|exists:environments,id,deleted_at,NULL', // Opcional, pero debe existir en la tabla 'environments'
        ];
    }
   public function withValidator(Validator $validator)
{
    $validator->after(function ($validator) {
        $type = $this->input('type');
        $sort = $this->input('sort');
        $environmentId = $this->input('environment_id');
        $id = $this->route('id');

        // Validar combinación de sort + type + environment
        if ($type && $sort !== null && $environmentId) {
            $query = Station::where('type', $type)
                ->where('sort', $sort)
                ->where('environment_id', $environmentId);

            if ($id) {
                $query->where('id', '!=', $id);
            }

            if ($query->exists()) {
                $validator->errors()->add('sort', "El orden {$sort} ya está ocupado en el croquis para el tipo {$type} dentro de este ambiente. Por favor, elige otro número.");
            }
        }

        // Validación especial para tipo BOX
        if ($type === 'BOX') {
            $price = $this->input('price');
            $unitPrice = $this->input('price_unitario');
            $people = $this->input('quantity_people');

            if (is_null($unitPrice)) {
                $validator->errors()->add('price_unitario', 'El campo precio unitario es obligatorio para el tipo BOX.');
            }

            if (is_null($people)) {
                $validator->errors()->add('quantity_people', 'El campo cantidad de personas es obligatorio para el tipo BOX.');
            }

            if (!is_null($unitPrice) && !is_null($people)) {
                $expectedPrice = $unitPrice * $people;
                if ((float) $price !== (float) $expectedPrice) {
                    $validator->errors()->add('price', "El precio total debe ser igual al producto de precio unitario por cantidad de personas ({$unitPrice} × {$people} = {$expectedPrice}).");
                }
            }
        }
    });
}


    public function messages()
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
    
            'description.string' => 'La Descripción debe ser una cadena de texto.',
            'description.max' => 'La Descripción no puede tener más de 255 caracteres.',

            'type.string' => 'El tipo debe ser una cadena de texto.',
            'type.max' => 'El tipo no puede tener más de 50 caracteres.',
    
            'status.string' => 'El estado debe ser una cadena.',
    
            'route.image' => 'El archivo debe ser una imagen.',
            'route.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max' => 'El archivo no puede ser mayor a 2 MB.',
    
            'environment_id.integer' => 'El identificador del ambiente debe ser un número entero.',
            'environment_id.exists' => 'El ambiente seleccionado no existe.',

            'price.numeric' => 'El campo precio debe ser un número.',
            'price.min' => 'El precio no puede ser menor que 0.',
            'sort.numeric' => 'El campo orden debe ser un número.',

            'sort.min' => 'El campo orden no puede ser negativo.',
        ];
    }
    

}
