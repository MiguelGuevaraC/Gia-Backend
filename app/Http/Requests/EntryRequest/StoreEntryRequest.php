<?php
namespace App\Http\Requests\EntryRequest;

use App\Http\Requests\StoreRequest;

class StoreEntryRequest extends StoreRequest
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
            'entry_datetime' => 'nullable|date', // Debe ser una fecha válida
            'code_pay'       => 'nullable|string|max:255',
            'quantity'       => 'nullable|string|max:255',
            'status_pay'     => 'nullable|string|max:255',
            'status_entry'   => 'nullable|string|max:255',
            'event_id'       => 'nullable|string|max:255|exists:events,id,deleted_at,NULL', // Asegura que el evento existe
            'person_id'      => 'nullable|string|max:255|exists:persons,id,deleted_at,NULL',   // Asegura que la persona existe
        ];
    }
    
    public function messages()
    {
        return [
            'name.required'         => 'El nombre es obligatorio.',
            'name.string'           => 'El nombre debe ser una cadena de texto.',
            'name.max'              => 'El nombre no puede tener más de 255 caracteres.',
            'entry_datetime.date'   => 'La fecha de entrada debe ser una fecha válida.',
            'code_pay.string'       => 'El código de pago debe ser una cadena de texto.',
            'code_pay.max'          => 'El código de pago no puede tener más de 255 caracteres.',
            'quantity.string'       => 'La cantidad debe ser una cadena de texto.',
            'quantity.max'          => 'La cantidad no puede tener más de 255 caracteres.',
       
            'status_pay.string'     => 'El estado del pago debe ser una cadena de texto.',
            'status_pay.max'        => 'El estado del pago no puede tener más de 255 caracteres.',
          
            'status_entry.string'   => 'El estado de la entrada debe ser una cadena de texto.',
            'status_entry.max'      => 'El estado de la entrada no puede tener más de 255 caracteres.',
            'event_id.string'       => 'El identificador del evento debe ser una cadena de texto.',
            'event_id.max'          => 'El identificador del evento no puede tener más de 255 caracteres.',
            'event_id.exists'       => 'El identificador del evento no existe.',
            'person_id.string'      => 'El identificador de la persona debe ser una cadena de texto.',
            'person_id.max'         => 'El identificador de la persona no puede tener más de 255 caracteres.',
            'person_id.exists'      => 'El identificador de la persona no existe.',
        ];
    }
    

}
