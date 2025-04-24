<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\UpdateRequest;
use App\Models\Promotion;
use Illuminate\Validation\Rule;

class UpdateReservationRequest extends UpdateRequest
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
            'name'                 => 'required|string|max:255',
            'reservation_datetime' => 'required|date',
            'nro_people'           => 'nullable|string|max:255',
            'event_id'             => 'nullable|string|max:255|exists:events,id,deleted_at,NULL',   // Verifica si el event_id existe en la tabla events
            'station_id'           => 'nullable|string|max:255|exists:stations,id,deleted_at,NULL', // Verifica si el station_id existe en la tabla stations
            'person_id'            => 'nullable|string|max:255|exists:people,id,deleted_at,NULL',   // Verifica si el person_id existe en la tabla persons
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('details')) {
                foreach ($this->input('details') as $index => $detail) {
                    // Validación de stock
                    $promotion = Promotion::where('id', $detail['id'])
                        ->whereNull('deleted_at')
                        ->first();

                    if (! $promotion) {
                        continue; // Ya validado por 'exists'
                    }

                    // Ejecutar la actualización del stock antes de la validación
                    $promotion->recalculateStockPromotion();

                    if ($promotion->stock_restante < $detail['cant']) {
                        $validator->errors()->add("details.$index.cant", "La promoción '{$promotion->name}' no tiene suficiente stock.");
                    }

                }
            }

            if ($this->filled('event_id')) {
                $mesaOcupada = \App\Models\Reservation::where('event_id', $this->input('event_id'))
                    ->where('station_id', $this->input('station_id'))
                    ->where('status', '!=', 'Caducado')
                    ->with(['station', 'event']) // Cargar relaciones
                    ->first();

                if ($mesaOcupada) {
                    $nombreMesa   = optional($mesaOcupada->station)->name ?? 'Mesa desconocida';
                    $nombreEvento = optional($mesaOcupada->event)->name ?? 'Evento desconocido';

                    $validator->errors()->add(
                        "details.$index.station_id",
                        "La mesa '{$nombreMesa}' ya está ocupada en el evento '{$nombreEvento}'."
                    );
                }
            }

        });
    }

}
