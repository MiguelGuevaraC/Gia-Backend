<?php
namespace App\Services;

use App\Models\DetailReservation;
use App\Models\Event;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\Setting;
use Carbon\Carbon;

class ReservationService
{
    protected $codeGeneratorService;
    protected $eventService;

    public function __construct(
        EventService $eventService,
        CodeGeneratorService $codeGeneratorService
    ) {
        $this->eventService = $eventService;
        $this->codeGeneratorService = $codeGeneratorService;
    }
    public function getReservationById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function createReservation(array $data): Reservation
    {
        $data['user_id'] = auth()->id();
        $precioreservaton = $data['precio_reservation'];
        $data['expires_at'] = now()->addMinutes(Setting::find(1)?->amount ?? 5);
        $details = $data['details'] ?? [];
        unset($data['details'], $data['precio_reservation']);

        // Crear evento diario si no hay uno existente y event_id es null
        if (empty($data['event_id'])) {
            $reservationDate = !empty($data['reservation_datetime'])
                ? Carbon::parse($data['reservation_datetime'])
                : now();

            $dayStart = $reservationDate->copy()->startOfDay();
            $dayEnd = $reservationDate->copy()->endOfDay();
            $companyId = $data['company_id'];

            $evento = Event::where('is_daily_event', '1')
                ->whereBetween('event_datetime', [$dayStart, $dayEnd])
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->first();

            if (!$evento) {
                $data['event_id'] = Event::create([
                    'name' => 'Evento ' . ucfirst($reservationDate->translatedFormat('l d/F/Y')),
                    'event_datetime' => $reservationDate,
                    'company_id' => $companyId,
                    'is_daily_event' => '1',
                ])->id;
            } else {
                $data['event_id'] = $evento->id;
            }
        }

        $data['status'] = (count($details) == 0 && floatval($precioreservaton) == 0)
            ? 'Pagado'
            : 'Pendiente Pago';

        $reservation = Reservation::create($data);

        DetailReservation::create([
            'cant' => 1,
            'name' => 'Servicio Reserva',
            'type' => 'reserva',
            'precio' => $precioreservaton,
            'precio_total' => $precioreservaton,
            'status' => 'Pendiente Pago',
            'promotion_id' => null,
            'reservation_id' => $reservation->id,
        ]);

        foreach ($details as $detail) {
            $promotion = Promotion::find($detail['id']);
            $cant = $detail['cant'] ?? 1;

            DetailReservation::create([
                'cant' => $cant,
                'name' => $promotion->name ?? '',
                'type' => 'promocion',
                'precio' => $promotion->precio ?? '',
                'precio_total' => $cant * ($promotion->precio ?? 0),
                'status' => 'Pendiente Pago',
                'promotion_id' => $detail['id'] ?? null,
                'reservation_id' => $reservation->id,
            ]);
        }

        return $reservation;
    }


    public function updateReservation(Reservation $reservation, array $data): Reservation
    {

        $reservation->update($data);

        return $reservation;
    }

    public function destroyById($id)
    {
        $Reservation = Reservation::find($id);

        if (!$Reservation) {
            return false;
        }
        return $Reservation->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
