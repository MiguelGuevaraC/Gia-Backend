<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrizeResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Price",
     *     title="Price",
     *     description="Price model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="event_name", type="string", example="Campaña Navidad"),
     *     @OA\Property(property="route", type="string", example="/sorteo/navidad"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-05T10:00:00Z")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event_name' => $this?->name,
            'route' => $this->route,
            'created_at' => $this->created_at,
        ];
    }
}
