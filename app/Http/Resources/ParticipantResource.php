<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username ?? null,
            'person' => $this->person ? new PersonResource($this->person) : null,
            'ticket_count' => $this->tickets->count(),
            'tickets' => $this->tickets->map(function ($ticket) {
                return [
                    'id_ticket' => $ticket->id,
                    'code' => $ticket?->codes ? new CodeResource($ticket->codes) : null,
                ];
            }),


        ];
    }
}
