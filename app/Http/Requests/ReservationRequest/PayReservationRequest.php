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
            'amount' => ['required', 'numeric', 'min:600'],
            'description' => ['nullable', 'string', 'min:5', 'max:80'],
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],

            // Se quitan event_id, station_id y details de aquí
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateFechaNoMayorEvento($validator);
            $this->validateFechaNoMenorHoy($validator);
            $this->validateAmountTotal($validator);
        });
    }

    private function getReservationFromRoute()
    {
        return \App\Models\Reservation::with(['event', 'station', 'detailReservations.promotion'])
            ->find($this->route('id'));
    }

    private function validateFechaNoMayorEvento($validator)
    {
        $reservation = $this->getReservationFromRoute();

        if ($reservation && $reservation->event && $this->filled('reservation_datetime')) {
            $reservationDatetime = strtotime($this->reservation_datetime);
            $eventDatetime = strtotime($reservation->event->event_datetime);

            if ($reservationDatetime > $eventDatetime) {
                $validator->errors()->add(
                    'reservation_datetime',
                    "La fecha de la reserva {$this->reservation_datetime} no puede ser posterior a la fecha del evento {$reservation->event->event_datetime}."
                );
            }
        }
    }

    private function validateFechaNoMenorHoy($validator)
    {
        if ($this->filled('reservation_datetime')) {
            $reservationDate = date('Y-m-d', strtotime($this->reservation_datetime));
            $todayDate = date('Y-m-d');

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
        $reservation = $this->getReservationFromRoute();

        if (!$reservation || !$reservation->event || !$reservation->station) {
            return $validator->errors()->add('amount', 'Reserva, evento o estación no encontrados.');
        }

        $totalReservation = 0;
        $totalPromotions = 0;

        if ($reservation->detailReservations) {
            foreach ($reservation->detailReservations as $detail) {
                $cantidad = intval($detail->cant);

                if ($detail->type === 'reserva') {
                    $precio = floatval($detail->precio); // se asume que tiene el campo 'precio' en el detail
                    $totalReservation += $precio * $cantidad;
                }

                if ($detail->type === 'promocion' && $detail->promotion) {
                    $precio = floatval($detail->promotion->precio);
                    $totalPromotions += $precio * $cantidad;
                }
            }
        }

        $expectedAmount = $totalReservation + $totalPromotions;
        $providedAmount = floatval($this->amount) / 100;

        if (round($expectedAmount, 2) !== round($providedAmount, 2)) {
            $validator->errors()->add(
                'amount',
                "El monto total ingresado S/. {$providedAmount} no es correcto. Debe ser igual a la suma del precio de la reserva S/. {$totalReservation} más el precio de las promociones seleccionadas S/. {$totalPromotions}, dando un total de S/. {$expectedAmount}."
            );
        }
    }


}
