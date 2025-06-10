<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LotteryResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Lottery",
     *     title="Lottery",
     *     description="Lottery model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="code_serie", type="string", example="SER-001"),
     *     @OA\Property(property="lottery_name", type="string", example="Sorteo Navideño"),
     *     @OA\Property(property="lottery_description", type="string", example="Premio por compras en diciembre"),
     *     @OA\Property(property="lottery_date", type="string", format="date-time", example="2025-12-24T20:00:00Z"),
     *     @OA\Property(property="status", type="string", example="Pendiente"),
     *     @OA\Property(property="winner_id", type="integer", nullable=true, example=15),
     *     @OA\Property(property="winner_name", type="string", nullable=true, example="Juan Pérez"),
     *     @OA\Property(property="user_created_id", type="integer", example=3),
     *     @OA\Property(property="user_created_name", type="string", example="Admin User"),
     *     @OA\Property(property="event_id", type="integer", nullable=true, example=2),
     *     @OA\Property(property="event_name", type="string", nullable=true, example="Campaña Navidad"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-05T10:00:00Z")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_serie' => $this->code_serie,
            'lottery_name' => $this->lottery_name,
            'lottery_description' => $this->lottery_description,
            'lottery_date' => $this->lottery_date,
            'lottery_price' => $this->lottery_price,
            'lottery_by_event' => $this->lotteryByEvent,
            'status' => $this->status,
            'winner_id' => $this->winner_id,
            'winner_name' => $this->winner?->name,
            'user_created_id' => $this->user_created_id,
            'user_created_name' => $this->user_created?->name,
            'event_id' => $this->event_id,
            'event_name' => $this?->event?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
