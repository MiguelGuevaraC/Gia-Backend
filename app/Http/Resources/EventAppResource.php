<?php
namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventAppResource extends JsonResource
{
/**
 * @OA\Schema(
 *     schema="EventApp",
 *     title="EventApp",
 *     description="Event model",
 *     @OA\Property(property="id", type="integer", example=1, description="ID del evento"),
 *     @OA\Property(property="name", type="string", example="Evento Deportivo", description="Nombre del evento"),
 *     @OA\Property(property="event_datetime", type="string", format="date-time", example="2025-01-16 18:00:00", description="Fecha y hora del evento"),
 *     @OA\Property(property="comment", type="string", example="Evento importante del año", description="Comentario del evento"),
 *     @OA\Property(property="status", type="string", example="Próximo", description="Estado del evento basado en la fecha"),
 *     @OA\Property(property="route", type="string", example="/eventos/1", description="Ruta del evento"),
 *     @OA\Property(property="pricebox", type="string", example="Precio Box", description="Precio Box"),
 *     @OA\Property(property="pricetable", type="string", example="Precio Mesas", description="Precio Mesas"),

 *     @OA\Property(property="user_id", type="integer", example=42, description="ID del usuario asociado"),
 *     @OA\Property(property="user", ref="#/components/schemas/User1"),
 *     @OA\Property(property="company_id", type="integer", example=42, description="ID de la empresa asociada"),
 *     @OA\Property(property="company", ref="#/components/schemas/Company")
 * )
 */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name ?? null,
            'event_datetime' => $this->event_datetime ?? null,
            'comment'        => $this->comment ?? null,
            'pricebox'       => $this->pricebox ?? null,
            'pricetable'     => $this->pricetable ?? null,
            'status'         => $this->event_datetime ? (Carbon::parse($this->event_datetime)->isFuture() ? 'Próximo' : 'Finalizó') : null,
            'route'          => $this->route ?? null,
        ];
    }

}
