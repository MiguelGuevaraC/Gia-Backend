<?php
namespace App\Services;

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
        // Agregar automáticamente el ID del usuario logueado
        $data['user_id'] = auth()->id(); // Obtiene el ID del usuario logueado
    
        $event = Reservation::create($data);
    
        return $event;
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
