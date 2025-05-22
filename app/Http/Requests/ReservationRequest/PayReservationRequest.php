<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Event;
use App\Models\Promotion;
use App\Models\Station;

class PayReservationRequest extends StoreRequest
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

                                                                        // Validaciones para el cargo con Culqi
            'amount'             => ['required', 'numeric', 'min:600'], // Monto debe ser numérico y mayor que 0
            'description'        => ['required', 'string', 'max:255'],  // Descripción es obligatoria y con un límite de caracteres
            'email'              => ['required', 'email'],              // Email debe ser válido
            'token'              => ['required', 'string'],             // Token no debe estar vacío

            'precio_reservation' => 'required|numeric|min:0',

            'event_id'           => 'required|exists:events,id,deleted_at,NULL',
            'station_id'         => 'required|exists:stations,id,deleted_at,NULL',

            'details'            => 'nullable|array',
            'details.*.id'       => 'required_with:details.*.cant|integer|exists:promotions,id,deleted_at,NULL',
            'details.*.cant'     => 'required_with:details.*.id|integer|min:1',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->filled('details')) {
            foreach ($this->input('details') as $detail) {
                $promotion = Promotion::where('id', $detail['id'] ?? null)
                    ->whereNull('deleted_at')
                    ->first();

                if ($promotion) {
                    // Actualizar el stock antes de validar
                    $promotion->recalculateStockPromotion();
                }
            }
        }
    }

    public function messages()
    {
        return [
            // Culqi
            'amount.required'               => 'El monto es obligatorio.',
            'amount.numeric'                => 'El monto debe ser un valor numérico.',
            'amount.min'                    => 'El monto debe ser como mínimo de S/. 600.',
            'description.required'          => 'La descripción es obligatoria.',
            'description.max'               => 'La descripción no debe superar los 255 caracteres.',
            'email.required'                => 'El correo electrónico es obligatorio.',
            'email.email'                   => 'El correo electrónico no tiene un formato válido.',
            'token.required'                => 'El token de pago es obligatorio.',

            //formulario validaciones
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validatePrecioReserva($validator);
            $this->validateFechaNoMayorEvento($validator);
            $this->validateFechaNoMenorHoy($validator);
            $this->validateAmountTotal($validator); // 👈 NUEVO
        });
    }

    private function validatePrecioReserva($validator)
    {
        if ($this->filled(['event_id', 'station_id', 'precio_reservation'])) {
            $event   = Event::find($this->event_id);
            $station = Station::find($this->station_id);

            if ($event && $station) {
                $expectedPrice = $station->type === 'MESA' ? $event->pricetable
                : ($station->type === 'BOX' ? $event->pricebox : null);

                if ($expectedPrice !== null && floatval($this->precio_reservation) != floatval($expectedPrice)) {
                    $validator->errors()->add(
                        'precio_reservation',
                        "El precio ingresado no coincide con el valor de la estación tipo '{$station->type}' en el evento '{$event->name}'. Debe ser: $expectedPrice."
                    );
                }
            }
        }
    }

    private function validateFechaNoMayorEvento($validator)
    {
        if ($this->filled(['event_id', 'reservation_datetime'])) {
            $event = Event::find($this->event_id);

            if ($event && $event->event_datetime) {
                $reservationDatetime = strtotime($this->reservation_datetime);
                $eventDatetime       = strtotime($event->event_datetime);

                if ($reservationDatetime > $eventDatetime) {
                    $validator->errors()->add(
                        'reservation_datetime',
                        "La fecha de la reserva {$this->reservation_datetime} no puede ser posterior a la fecha del evento {$event->event_datetime}."
                    );
                }
            }
        }
    }

    private function validateFechaNoMenorHoy($validator)
    {
        if ($this->filled('reservation_datetime')) {
            // Extraer solo la fecha (sin hora)
            $reservationDate = date('Y-m-d', strtotime($this->reservation_datetime));
            $todayDate       = date('Y-m-d');

            if ($reservationDate < $todayDate) {
                $validator->errors()->add(
                    'reservation_datetime',
                    "La fecha de la reserva {$reservationDate} no puede ser anterior a la fecha de hoy {$todayDate}."
                );
            }
        }
    }

    private function validateAmountTotal($validator)
    {
        $precioReservation = floatval($this->precio_reservation);
        $totalPromotions   = 0;

        if ($this->filled('details')) {
            foreach ($this->details as $detail) {
                $promotion = Promotion::where('id', $detail['id'] ?? null)
                    ->whereNull('deleted_at')
                    ->first();

                if ($promotion && isset($detail['cant'])) {
                    $totalPromotions += floatval($promotion->precio) * intval($detail['cant']);
                }
            }
        }

        $expectedAmount = $precioReservation + $totalPromotions;
        $providedAmount = floatval($this->amount) / 100;

        if (round($expectedAmount, 2) !== round($providedAmount, 2)) {
            $validator->errors()->add(
                'amount',
                "El monto total ingresado S/. {$providedAmount} no es correcto. Debe ser igual a la suma del precio de la reserva S/. {$precioReservation} más el precio de las promociones seleccionadas S/. {$totalPromotions}, dando un total de S/. {$expectedAmount}."
            );
        }

    }

}
