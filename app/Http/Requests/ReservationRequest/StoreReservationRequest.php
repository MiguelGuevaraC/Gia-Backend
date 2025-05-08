<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Promotion;

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
            'precio_reservation'   => 'required|numeric|min:0',

            'event_id'             => 'required|string|max:255|exists:events,id,deleted_at,NULL',
            'station_id'           => 'required|string|max:255|exists:stations,id,deleted_at,NULL',
            'person_id'            => 'required|string|max:255|exists:people,id,deleted_at,NULL',

            'details'              => 'nullable|array',
            'details.*.id'         => 'required_with:details.*.cant|integer|exists:promotions,id,deleted_at,NULL',
            'details.*.cant'       => 'required_with:details.*.id|integer|min:1',
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
                    $promotion->find($promotion->id);
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

            'precio_reservation.required'   => 'El campo precio de reserva es obligatorio.',
            'precio_reservation.numeric'    => 'El campo precio de reserva debe ser un número.',
            'precio_reservation.min'        => 'El precio de reserva no puede ser menor que 0.',
        ];
    }

}
