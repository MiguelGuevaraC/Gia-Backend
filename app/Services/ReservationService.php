<?php
namespace App\Services;

use App\Models\DetailReservation;
use App\Models\Promotion;
use App\Models\Reservation;
use Illuminate\Support\Facades\Storage;

class ReservationService
{

    public function getReservationById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function createReservation(array $data): Reservation
    {
        $data['user_id'] = auth()->id();
        $precioreservaton= $data['precio_reservation']; // Eliminamos para evitar error en el fillable
        // Extraer los detalles antes de crear la reserva
        $details = $data['details'] ?? []; // esto debería ser un array de detalles
        unset($data['details']); // Eliminamos para evitar error en el fillable
        unset($data['precio_reservation']);

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
            $promotion= Promotion::find($detail['id']);
            $cant=$detail['cant']??1;
            DetailReservation::create([
                'cant' => $cant,
                'name' => $promotion->name?? '',
                'type' => 'promocion',
                'precio' =>$promotion->precio?? '',
                'precio_total' => $cant*$promotion->precio?? '',
                'status' => 'Activo',
                'promotion_id' => $detail['id'] ?? null,
                'reservation_id' => $reservation->id,
            ]);
        }
    
        return $reservation;
    }

    public function updateReservation(Reservation $environment, array $data): Reservation
    {
     

        $environment->update($data);

        return $environment;
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
