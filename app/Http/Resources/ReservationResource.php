<?php
namespace App\Http\Resources;

use Carbon\Carbon;
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
            'correlative'          => 'R001-' . str_pad($this->id, 9, '0', STR_PAD_LEFT),
            'name'                 => $this->name ?? null,
            'reservation_datetime' => $this->reservation_datetime ?? null,

            'nroPeople'            => $this->nro_people ?? null,

            // 'status'               => $this->reservation_datetime ? (Carbon::parse($this->reservation_datetime)->isFuture() ? 'Reservado' : 'Finalizó') : null,
            'status'               => $this->status ?? null,

            'total'                => $this->detailReservations()->sum('precio_total') ?? null,

            'user_id'              => $this->user_id ?? null,
            'user'                 => $this->user ? $this->user : null,
            'person_id'            => $this->person_id ?? null,
            'person'               => $this->person ? new PersonResource($this->person) : null,
            'event_id'             => $this->event_id ?? null,
            'event'                => $this->event ? new EventResource($this->event) : null,
            'station_id'           => $this->station_id ?? null,
            'station'              => $this->station ? $this->station : null,
            'detailReservations'   => $this->detailReservations ? $this->detailReservations : null,
            'created_at'           => $this->created_at->format('Y-m-d H:i:s'),
            'expires_at'           => $this->expires_at != null
            ? Carbon::parse($this->expires_at)->format('Y-m-d H:i:s')
            : null,
        ];
    }

}
