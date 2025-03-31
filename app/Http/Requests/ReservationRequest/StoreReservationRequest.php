<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\StoreRequest;

class StoreReservationRequest extends StoreRequest
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
            'name'                 => 'required|string|max:255',
            'reservation_datetime' => 'required|date',
            'nro_people'           => 'nullable|string|max:255',

            'event_id'             => 'required|string|max:255|exists:events,id,deleted_at,NULL',   // Verifica si el event_id existe en la tabla events
            'station_id'           => 'required|string|max:255|exists:stations,id,deleted_at,NULL', // Verifica si el station_id existe en la tabla stations
            'person_id'            => 'required|string|max:255|exists:people,id,deleted_at,NULL',   // Verifica si el person_id existe en la tabla persons
        ];
    }

    public function messages()
    {
        return [
            'name.required'                 => 'El nombre es obligatorio.',
            'name.string'                   => 'El nombre debe ser un texto válido.',
            'name.max'                      => 'El nombre no puede superar los 255 caracteres.',
    
            'reservation_datetime.required' => 'La fecha de reserva es obligatoria.',
            'reservation_datetime.date'     => 'La fecha de reserva debe tener un formato válido.',
    
            'nro_people.string'             => 'El número de personas debe ser un texto válido.',
            'nro_people.max'                => 'El número de personas no puede superar los 255 caracteres.',
    
            'event_id.string'               => 'El identificador del evento debe ser un texto válido.',
            'event_id.max'                  => 'El identificador del evento no puede superar los 255 caracteres.',
            'event_id.exists'               => 'El evento seleccionado no existe en la base de datos.',
    
            'station_id.string'             => 'El identificador de la estación debe ser un texto válido.',
            'station_id.max'                => 'El identificador de la estación no puede superar los 255 caracteres.',
            'station_id.exists'             => 'La estación seleccionada no existe en la base de datos.',
    
            'person_id.string'              => 'El identificador de la persona debe ser un texto válido.',
            'person_id.max'                 => 'El identificador de la persona no puede superar los 255 caracteres.',
            'person_id.exists'              => 'La persona seleccionada no existe en la base de datos.',
        ];
    }
    

}
