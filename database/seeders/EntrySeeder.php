<?php
namespace Database\Seeders;

use App\Models\Entry;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class EntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public function run()
     {
        $reservations = Reservation::all(); // O algún filtro más específico
    
        foreach ($reservations as $reservation) {
            // Crear dos entradas para cada reserva
            for ($i = 0; $i < 2; $i++) {
                Entry::create([
                    'entry_datetime' => $reservation->reservation_datetime, // Fecha de entrada, puedes ajustarla como quieras
                  
                    'quantity' => 1, // Cantidad aleatoria de personas o productos
                    'status_pay' => $i === 0 ? 'Pendiente' : 'Pagado', // Estado de pago (alternar entre Pendiente y Pagado)
                    'status_entry' => 'No Ingresado', // Estado de entrada
                    'user_id' => 1, // ID del usuario de la reserva
                    'event_id' => $reservation->event_id, // ID del evento de la reserva
                    'person_id' => $reservation->person_id, // ID de la persona asociada a la reserva
                ]);
            }
        }
 
     }
     

}
