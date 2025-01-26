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

     *     @OA\Property(property="status", type="string", example="Activo", description="Estado del evento"),
     *     @OA\Property(property="user_id", type="integer", example=42, description="ID del usuario asociado"),
     *      @OA\Property(property="user", ref="#/components/schemas/User1"),
     *      @OA\Property(property="company_id", type="integer", example=42, description="ID del usuario asociado"),
     *      @OA\Property(property="company", ref="#/components/schemas/Company"),
     * )
     */

    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name ?? null,
            'event_datetime' => $this->event_datetime ?? null,
            'comment'        => $this->comment ?? null,
            'status'         => $this->event_datetime ? (Carbon::parse($this->event_datetime)->isFuture() ? 'Próximo' : 'Finalizó') : null,

            'user_id'        => $this->user_id ?? null,
            'user'           => $this->user ? new UserOnlyResource($this->user) : null,

            'company_id'        => $this->company_id ?? null,
            'company'           => $this->company ? new CompanyResource($this->company) : null,
        ];
    }

}
