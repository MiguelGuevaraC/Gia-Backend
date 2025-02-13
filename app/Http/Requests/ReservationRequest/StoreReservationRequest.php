<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Station;

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
            'name'           => 'sometimes|string|max:255',
            'type'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|string|max:50',
            'status'         => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    $id      = $this->route('id'); // Obtiene el ID de la estación desde la ruta
                    $station = Station::find($id); // Busca la estación en la base de datos

                    if ($station && $station->status === 'Ocupada' && $value !== 'Ocupada') {
                        $fail('No se puede cambiar el estado de una estación Ocupada a otro estado.');
                    }
                },
            ],
            'route'          => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048',
            'environment_id' => 'sometimes|integer|exists:environments,id,deleted_at,NULL',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                 => 'El nombre es obligatorio.',
            'name.string'                   => 'El nombre debe ser una cadena de texto.',
            'name.max'                      => 'El nombre no puede tener más de 255 caracteres.',

            'reservation_datetime.required' => 'La fecha de reserva es obligatorio.',
            'reservation_datetime.date'     => 'La fecha de reserva debe ser una fecha.',

            'nro_people.string'             => 'El número de personas debe ser una cadena de texto.',
            'nro_people.max'                => 'El número de personas no puede tener más de 255 caracteres.',

            'status.string'                 => 'El estado debe ser una cadena de texto.',
            'status.max'                    => 'El estado no puede tener más de 255 caracteres.',

            'event_id.string'               => 'El identificador del evento debe ser una cadena de texto.',
            'event_id.max'                  => 'El identificador del evento no puede tener más de 255 caracteres.',
            'event_id.exists'               => 'El evento seleccionado no existe en la base de datos.', // Mensaje personalizado para el error de event_id

            'station_id.string'             => 'El identificador de la estación debe ser una cadena de texto.',
            'station_id.max'                => 'El identificador de la estación no puede tener más de 255 caracteres.',
            'station_id.exists'             => 'La estación seleccionada no existe en la base de datos.', // Mensaje personalizado para el error de station_id

            'person_id.string'              => 'El identificador de la persona debe ser una cadena de texto.',
            'person_id.max'                 => 'El identificador de la persona no puede tener más de 255 caracteres.',
            'person_id.exists'              => 'La persona seleccionada no existe en la base de datos.', // Mensaje personalizado para el error de person_id
        ];
    }

}
