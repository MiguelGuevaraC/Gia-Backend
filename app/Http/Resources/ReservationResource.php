<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
/**
 * @OA\Schema(
 *     schema="Reservation",
 *     title="Reservation",
 *     description="Modelo de reservación",
 *     @OA\Property(property="id", type="integer", example=1, description="Identificador único de la reservación"),
 *     @OA\Property(property="correlative", type="string", example="RES-001", description="Número correlativo de la reservación"),
 *     @OA\Property(property="name", type="string", example="John Doe", description="Nombre asociado a la reservación"),
 *     @OA\Property(property="reservation_datetime", type="string", example="2025-01-16 14:30:00", description="Fecha y hora de la reservación"),
 *     @OA\Property(property="nro_people", type="string", example="4", description="Número de personas asociadas a la reservación"),
 *     @OA\Property(property="status", type="string", example="Confirmado", description="Estado de la reservación"),
 *     @OA\Property(property="user_id", type="integer", example=42, description="ID del usuario que realizó la reservación"),
 *     @OA\Property(property="user", ref="#/components/schemas/User", description="Detalles del usuario que realizó la reservación"),
 *     @OA\Property(property="person_id", type="integer", example=7, description="ID de la persona asociada a la reservación"),
 *     @OA\Property(property="person", ref="#/components/schemas/Person", description="Detalles de la persona asociada a la reservación"),
 *     @OA\Property(property="event_id", type="integer", example=15, description="ID del evento relacionado a la reservación"),
 *     @OA\Property(property="event", ref="#/components/schemas/Event", description="Detalles del evento relacionado"),
 *     @OA\Property(property="station_id", type="integer", example=5, description="ID de la estación relacionada a la reservación"),
 *     @OA\Property(property="station", ref="#/components/schemas/Station", description="Detalles de la estación relacionada"),
 * )
 */

    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'correlative'          => $this->correlative ?? 'Sin Correlativo',
            'name'                 => $this->name ?? 'Sin Correlativo',
            'reservation_datetime' => $this->reservation_datetime ?? 'Sin Fecha',
            'nro_people'           => $this->nro_people ?? 'Sin Nro Personas',
            'status'               => $this->status ?? 'Sin Estado',
            'user_id'              => $this->user_id ?? 'Sin ID Usuario',
            'user'                 => $this->user ? $this->user : 'Sin Usuario',
            'person_id'            => $this->person_id ?? 'Sin ID Persona',
            'person'               => $this->person ? new PersonResource($this->person) : 'Sin Persona',
            'event_id'             => $this->event_id ?? 'Sin ID Persona',
            'event'                => $this->event ? new EventResource($this->event) : 'Sin Evento',
            'station_id'           => $this->station_id ?? 'Sin ID Estación',
            'station'              => $this->station ? new $this->station : 'Sin Estación',
        ];
    }

}
