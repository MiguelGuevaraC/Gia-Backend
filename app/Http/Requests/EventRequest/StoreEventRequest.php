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
            'status'         => 'nullable|string',          // Asegurar que sea true o false
            'company_id' => 'required|integer|exists:companies,id,deleted_at,NULL',
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

         

            'status.string'           => 'El estado debe ser una cadena.',
'company_id.required' => 'La compañía es obligatoria.',
            'company_id.integer' => 'El identificador de la compañía debe ser un número entero.',
            'company_id.exists' => 'La compañía seleccionada no existe.',
        ];
    }

}
