<?php
namespace App\Services;

use App\Models\Event;

class EventService
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }
    public function getEventById(int $id): ?Event
    {
        return Event::find($id);
    }

    public function createEvent(array $data): Event
    {
                                         // Agregar automáticamente el ID del usuario logueado
        $data['user_id'] = auth()->id(); // Obtiene el ID del usuario logueado

        $event = Event::create($data);
        $this->commonService->store_photo($data, $event, 'events');

        return $event;
    }

    public function updateEvent(Event $environment, array $data): Event
    {
        if (isset($data['route'])) {
            $data['route'] = $this->commonService->update_photo($data, $environment, 'events');
        }
        $data = array_intersect_key($data, $environment->getAttributes());
        $environment->update($data);

        return $environment;
    }

    public function destroyById($id)
    {
        $Event = Event::find($id);

        if (! $Event) {
            return false;
        }
        return $Event->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
