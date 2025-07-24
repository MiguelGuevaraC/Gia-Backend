<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Event;
use App\Models\Promotion;
use App\Models\Reservation;
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
            'name' => 'nullable|string|max:255',
            'reservation_datetime' => 'required|date',
            'nro_people' => 'nullable|string|max:255',
            //'precio_reservation'   => 'required|numeric|min:0',

            'event_id' => 'nullable|string|exists:events,id,deleted_at,NULL',
            'company_id' => 'nullable|string|exists:companies,id,deleted_at,NULL',

            'station_id' => 'required|string|exists:stations,id,deleted_at,NULL',
            'person_id' => 'required|string|exists:people,id,deleted_at,NULL',
            'details' => 'nullable|array',
            'details.*.id' => 'required_with:details.*.cant|integer|exists:promotions,id,deleted_at,NULL',
            'details.*.cant' => 'required_with:details.*.id|integer|min:1',
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
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            'reservation_datetime.required' => 'La fecha de reserva es obligatoria.',
            'reservation_datetime.date' => 'La fecha de reserva debe tener un formato válido.',

            'nro_people.string' => 'El número de personas debe ser un texto válido.',
            'nro_people.max' => 'El número de personas no puede superar los 255 caracteres.',

            'event_id.string' => 'El identificador del evento debe ser un texto válido.',
            'event_id.max' => 'El identificador del evento no puede superar los 255 caracteres.',
            'event_id.exists' => 'El evento seleccionado no existe en la base de datos.',

            'station_id.string' => 'El identificador de la estación debe ser un texto válido.',
            'station_id.max' => 'El identificador de la estación no puede superar los 255 caracteres.',
            'station_id.exists' => 'La estación seleccionada no existe en la base de datos.',

            'person_id.string' => 'El identificador de la persona debe ser un texto válido.',
            'person_id.max' => 'El identificador de la persona no puede superar los 255 caracteres.',
            'person_id.exists' => 'La persona seleccionada no existe en la base de datos.',

            'precio_reservation.required' => 'El campo precio de reserva es obligatorio.',
            'precio_reservation.numeric' => 'El campo precio de reserva debe ser un número.',
            'precio_reservation.min' => 'El precio de reserva no puede ser menor que 0.',

            'company_id.required' => 'El campo empresa es obligatorio.',
            'company_id.string' => 'El campo empresa debe ser una cadena de texto.',
            'company_id.exists' => 'La empresa seleccionada no existe o ha sido eliminada.',

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            if (empty($data['event_id']) && (empty($data['company_id']) || !isset($data['company_id']))) {
                $validator->errors()->add('company_id', 'El campo company_id es obligatorio cuando no se proporciona un event_id.');
            }

            $this->validateStock($validator);
            $this->validateMesaOcupada($validator);

            //$this->validateFechaNoMayorEvento($validator);
            $this->validateFechaNoMenorHoy($validator);
            $this->validateFechaNoMayor14Dias($validator);
            $this->validateNoCoincideConEventoActivo($validator);
        });
    }

    private function validateFechaNoMayor14Dias($validator)
    {
        if ($this->filled('reservation_datetime') && empty($this->event_id)) {
            $reservationDate = strtotime($this->reservation_datetime);
            $today = strtotime(date('Y-m-d'));
            $maxDate = strtotime('+14 days', $today);

            if ($reservationDate > $maxDate) {
                $diffDays = ceil(($reservationDate - $maxDate) / (60 * 60 * 24));
                $validator->errors()->add(
                    'reservation_datetime',
                    "La fecha de la reserva no puede ser mayor a 14 días desde hoy. Te has pasado por {$diffDays} día(s)."
                );
            }
        }
    }

    private function validateNoCoincideConEventoActivo($validator)
    {
        if ($this->filled('reservation_datetime') && empty($this->event_id)) {
            $reservationDateStr = date('Y-m-d', strtotime($this->reservation_datetime));

            $evento = Event::whereDate('event_datetime', $reservationDateStr)
                ->where('is_daily_event', '0')
                ->whereNull('deleted_at')
                ->first();

            if ($evento) {
                $validator->errors()->add(
                    'reservation_datetime',
                    "No se puede reservar para el día {$reservationDateStr} porque ya existe un evento activo llamado '{$evento->name}' programado en esa fecha."
                );
            }
        }
    }



    private function validateStock($validator)
    {
        if ($this->filled('details')) {
            foreach ($this->input('details') as $index => $detail) {
                $promotion = Promotion::where('id', $detail['id'])
                    ->whereNull('deleted_at')
                    ->first();

                if (!$promotion) {
                    continue; // Ya validado por 'exists'
                }

                $promotion->recalculateStockPromotion();

                if ($promotion->stock_restante < $detail['cant']) {
                    $validator->errors()->add("details.$index.cant", "La promoción '{$promotion->name}' no tiene suficiente stock.");
                }
            }
        }
    }

    private function validateMesaOcupada($validator)
    {
        if ($this->filled('station_id')) {
            $eventId = $this->input('event_id');

            // Buscar evento activo del día si no se envía
            if ($eventId == null) {
                $todayStart = now()->startOfDay();
                $todayEnd = now()->endOfDay();

                $event = Event::where('is_daily_event', true)
                    ->whereBetween('event_datetime', [$todayStart, $todayEnd])
                    ->first();


                $eventId = $event?->id;
            }

            if ($eventId) {
                $mesaOcupada = Reservation::where('event_id', $eventId)
                    ->where('station_id', $this->input('station_id'))
                    ->where('status', '!=', 'Caducado')
                    ->with(['station', 'event'])
                    ->first();

                if ($mesaOcupada) {
                    $nombreMesa = optional($mesaOcupada->station)->name ?? 'Mesa desconocida';
                    $nombreEvento = optional($mesaOcupada->event)->name ?? 'Evento desconocido';

                    $validator->errors()->add(
                        'station_id',
                        "La mesa '{$nombreMesa}' ya está ocupada en el evento '{$nombreEvento}'."
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
                $eventDatetime = strtotime($event->event_datetime);

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
            $todayDate = date('Y-m-d');

            if ($reservationDate < $todayDate) {
                $validator->errors()->add(
                    'reservation_datetime',
                    "La fecha de la reserva {$reservationDate} no puede ser anterior a la fecha de hoy {$todayDate}."
                );
            }
        }
    }

}
