<?php
namespace App\Services;

use App\Models\DetailReservation;
use App\Models\Event;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\Setting;

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
        $precioreservaton = $data['precio_reservation']; // Eliminamos para evitar error en el fillable
        $data['expires_at'] = now()->addMinutes(Setting::find(1)?->amount ?? 5);
        // Extraer los detalles antes de crear la reserva
        $details = $data['details'] ?? []; // esto debería ser un array de detalles
        unset($data['details']);                                     // Eliminamos para evitar error en el fillable
        unset($data['precio_reservation']);

        // Crear evento diario si no hay uno existente y event_id es null
        if (empty($data['event_id'])) {
            $today = now()->endOfDay();
            $companyId = $data['company_id'];

            $eventoHoy = Event::whereDate('event_datetime', $today)
                ->where('company_id', $companyId)
                ->where('is_daily_event', true)
                ->whereNull('deleted_at')
                ->first();

            if (!$eventoHoy) {
                $data['event_id'] = Event::create([
                    'name' => 'Evento ' . ucfirst(now()->translatedFormat('l d/F/Y')),
                    'event_datetime' => $today,
                    'company_id' => $companyId,
                    'is_daily_event' => true,
                ])->id;
            } else {
                $data['event_id'] = $eventoHoy->id;
            }
        }

        if ((count($details) == 0) && floatval($precioreservaton) == 0) {
            $data['status'] = 'Pagado';
        } else {
            $data['status'] = 'Pendiente Pago';
        }

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
            $detail = DetailReservation::create([
                'cant' => $cant,
                'name' => $promotion->name ?? '',
                'type' => 'promocion',
                'precio' => $promotion->precio ?? '',
                'precio_total' => $cant * $promotion->precio ?? '',
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
