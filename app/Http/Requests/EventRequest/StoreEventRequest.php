<?php
namespace App\Http\Requests\EventRequest;

use App\Http\Requests\StoreRequest;

class StoreEventRequest extends StoreRequest
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
            'name'           => 'required|string|max:255',
            'event_datetime' => 'required|date',            // Debe ser una fecha válida
            'comment'        => 'nullable|string|max:1000', // Permitir comentarios opcionales
            'nro_reservas'   => 'nullable|integer|min:0',   // Número de reservas, no negativo
            'nro_boxes'      => 'nullable|integer|min:0',   // Número de boxes, no negativo
            'status'         => 'nullable|string',          // Asegurar que sea true o false
        ];
    }

    public function messages()
    {
        return [
            'id.integer'              => 'El ID debe ser un número entero.',

            'name.required'           => 'El nombre es obligatorio.',
            'name.string'             => 'El nombre debe ser una cadena de texto.',
            'name.max'                => 'El nombre no puede tener más de 255 caracteres.',

            'event_datetime.required' => 'La fecha y hora del evento son obligatorias.',
            'event_datetime.date'     => 'La fecha y hora deben ser válidas.',

            'comment.string'          => 'El comentario debe ser una cadena de texto.',
            'comment.max'             => 'El comentario no puede tener más de 1000 caracteres.',

            'nro_reservas.integer'    => 'El número de reservas debe ser un número entero.',
            'nro_reservas.min'        => 'El número de reservas no puede ser negativo.',

            'nro_boxes.integer'       => 'El número de boxes debe ser un número entero.',
            'nro_boxes.min'           => 'El número de boxes no puede ser negativo.',

            'status.string'           => 'El estado debe ser una cadena.',

        ];
    }

}
