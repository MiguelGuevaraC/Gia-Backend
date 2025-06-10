<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="LotteryEventByTicket",
 *     title="Lottery Ticket",
 *     description="Lottery ticket resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="price_factor_consumo", type="number", format="float", example=100.50),
 *     @OA\Property(property="event_id", type="integer", example=7),
 *     @OA\Property(property="event_name", type="string", example="Evento Especial"),
 *     @OA\Property(property="lottery_id", type="integer", example=3),
 *     @OA\Property(property="lottery_name", type="string", example="Sorteo Día del Padre"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-05T10:00:00Z")
 * )
 */
class LotteryByEventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'price_factor_consumo' => $this->pivot?->price_factor_consumo ?? null,
            'event_id' => $this->pivot?->event_id ?? $this->id,
            'lottery_id' => $this->pivot?->lottery_id ?? null,
        ];
    }
}
