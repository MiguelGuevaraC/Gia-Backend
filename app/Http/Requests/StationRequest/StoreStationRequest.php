<?php

namespace App\Http\Requests\StationRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Station;
use Illuminate\Validation\Validator;

class StoreStationRequest extends StoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:50',
            'status' => 'nullable|string',
            'type' => 'required|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'price_unitario' => 'nullable|numeric|min:0',
            'quantity_people' => 'nullable|numeric|min:0',
            'sort' => 'nullable|numeric|min:0',
            'environment_id' => 'required|integer|exists:environments,id,deleted_at,NULL',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $sort = $this->input('sort');
            $environmentId = $this->input('environment_id');
            $id = $this->route('id');

            // Validación para evitar duplicados en sort + type + environment_id
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

            // Validaciones específicas para BOX
            if (strtoupper($type) === 'BOX') {
                $price = $this->input('price');
                $unit = $this->input('price_unitario');
                $qty = $this->input('quantity_people');

                if (is_null($unit)) {
                    $validator->errors()->add('price_unitario', 'El precio unitario es obligatorio para estaciones tipo BOX.');
                }

                if (is_null($qty)) {
                    $validator->errors()->add('quantity_people', 'La cantidad de personas es obligatoria para estaciones tipo BOX.');
                }

                if (!is_null($unit) && !is_null($qty)) {
                    $expectedPrice = $unit * $qty;
                    if ((float)$price !== (float)$expectedPrice) {
                        $validator->errors()->add('price', "El precio debe ser igual a precio_unitario × cantidad de personas ({$unit} × {$qty} = {$expectedPrice}).");
                    }
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

            'status.string' => 'El estado debe ser una cadena.',

            'environment_id.required' => 'El ambiente es obligatorio.',
            'environment_id.integer' => 'El identificador del ambiente debe ser un número entero.',
            'environment_id.exists' => 'El ambiente seleccionado no existe.',

            'price.numeric' => 'El campo precio debe ser un número.',
            'price.min' => 'El precio no puede ser menor que 0.',
            'sort.numeric' => 'El campo orden debe ser un número.',
            'sort.min' => 'El campo orden no puede ser negativo.',
        ];
    }
}
