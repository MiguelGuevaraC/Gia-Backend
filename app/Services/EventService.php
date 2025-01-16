<?php
namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventService
{

    public function getEventById(int $id): ?Event
    {
        return Event::find($id);
    }

    public function createEvent(array $data): Event
    {
        // Agregar automáticamente el ID del usuario logueado
        $data['user_id'] = auth()->id(); // Obtiene el ID del usuario logueado
    
        $event = Event::create($data);
    
        return $event;
    }

    public function updateEvent(Event $environment, array $data): Event
    {
     

        $environment->update($data);

        return $environment;
    }

    public function destroyById($id)
    {
        $Event = Event::find($id);

        if (!$Event) {
            return false;
        }
        return $Event->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
