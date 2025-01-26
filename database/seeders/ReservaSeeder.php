<?php
namespace Database\Seeders;

use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public function run()
     {
         // Obtención de los 5 eventos existentes
         $events = Event::all();
     
         foreach ($events as $event) {
             // Crear 2 reservas para cada evento
             foreach (range(1, 2) as $index) {
                 Reservation::create([
                     'name' => "Reserva para el evento " . $event->name,
                     'reservation_datetime' => $event->event_datetime, // Fecha aleatoria dentro de los próximos 5 días
                     'nro_people' => rand(1, 10), // Número aleatorio de personas entre 1 y 10
                     'status' => 'Reservado', // Estado de la reserva
                     'user_id' => 1, // Usuario asociado a la reserva
                     'event_id' => $event->id, // Asignamos el ID del evento actual
                     'station_id' => rand(1, 4), // ID de estación aleatorio (ajustar según las estaciones disponibles)
                     'person_id' => 1, // ID de persona aleatorio (ajustar según las personas disponibles)
                 ]);
             }
         }
     }
     

}
