<?php

namespace App\Http\Requests\StationRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Station;
use Illuminate\Validation\Validator;

class StoreStationRequest extends StoreRequest
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
            'type' => 'required|string|max:50', // Limitar la longitud del tipo
            'description' => 'nullable|string|max:50', // Limitar la longitud del tipo
            'status' => 'nullable|string', // Asegurar que sea true o false

            'price' => 'nullable|numeric|min:0',
            'sort' => 'nullable|numeric',

            'environment_id' => 'required|integer|exists:environments,id,deleted_at,NULL', // Validar que exista en la tabla 'environments'
        ];
    }

    public function withValidator(Validator $validator)
{
    $validator->after(function ($validator) {
        $type = $this->input('type');
        $sort = $this->input('sort');
        $id   = $this->route('id'); // Asume que la ruta tiene un {id}

        if ($type && $sort !== null) {
            $query = Station::where('type', $type)->where('sort', $sort);
            if ($id) $query->where('id', '!=', $id);

            if ($query->exists()) {
                $validator->errors()->add('sort', "El orden {$sort} ya está registrado para el tipo {$type}.");
            }

            $ultimo = Station::where('type', $type)->max('sort');

            if ($sort > 1 && $sort != ($ultimo + 1)) {
                $validator->errors()->add('sort', "El siguiente orden disponible para el tipo {$type} es ".($ultimo + 1).".");
            }
        }
    });
}

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',

            'description.string' => 'La Descripción debe ser una cadena de texto.',
            'description.max' => 'La Descripción no puede tener más de 255 caracteres.',

            'type.required' => 'El tipo es obligatorio.',
            'type.string' => 'El tipo debe ser una cadena de texto.',
            'type.max' => 'El tipo no puede tener más de 50 caracteres.',

            'status.required' => 'El estado es obligatorio.',
            'status.string' => 'El estado debe ser una cadena.',

            'environment_id.required' => 'El ambiente es obligatorio.',
            'environment_id.integer' => 'El identificador del ambiente debe ser un número entero.',
            'environment_id.exists' => 'El ambiente seleccionado no existe.',

            'price.numeric' => 'El campo precio debe ser un número.',
            'price.min' => 'El precio no puede ser menor que 0.',
            'sort.numeric' => 'El campo orden debe ser un número.',
        ];
    }

}
