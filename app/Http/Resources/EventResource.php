<?php
namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Event",
     *     title="Event",
     *     description="Event model",
     *     @OA\Property(property="id", type="integer", example=1, description="ID del evento"),
     *     @OA\Property(property="name", type="string", example="Evento Deportivo", description="Nombre del evento"),
     *     @OA\Property(property="event_datetime", type="string", example="2025-01-16 18:00:00", description="Fecha y hora del evento"),
     *     @OA\Property(property="comment", type="string", example="Evento importante del año", description="Comentario del evento"),
     *     @OA\Property(property="nro_reservas", type="string", example="5", description="Número de reservas asociadas al evento"),
     *     @OA\Property(property="nro_boxes", type="string", example="3", description="Número de boxes reservados para el evento"),
     *     @OA\Property(property="status", type="string", example="Activo", description="Estado del evento"),
     *     @OA\Property(property="user_id", type="integer", example=42, description="ID del usuario asociado"),
     *      @OA\Property(property="user", ref="#/components/schemas/User1"),
     * )
     */

    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name ?? null,
            'event_datetime' => $this->event_datetime ?? null,
            'comment'        => $this->comment ?? null,
            'status'         => Carbon::parse($this->event_datetime)->isFuture() ? 'Próximo' : 'Finalizó',
            'user_id'        => $this->user_id ?? null,
            'user'           => $this->user ? new UserOnlyResource($this->user) : null,
        ];
    }

}
