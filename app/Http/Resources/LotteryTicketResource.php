<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LotteryTicketResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="LotteryTicket",
     *     title="Lottery Ticket",
     *     description="Lottery ticket resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="code_correlative", type="string", example="LTK-001"),
     *     @OA\Property(property="reason", type="string", example="Participación en evento"),
     *     @OA\Property(property="status", type="string", example="Ganador"),
     *     @OA\Property(property="user_owner_id", type="integer", example=5),
     *     @OA\Property(property="user_owner_name", type="string", example="Juan Pérez"),
     *     @OA\Property(property="lottery_id", type="integer", example=3),
     *     @OA\Property(property="lottery_name", type="string", example="Sorteo Día del Padre"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-05T10:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-05T12:00:00Z")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code_correlative' => $this->code_correlative,
            'reason' => $this->reason,
            'status' => $this->status,
            'user_owner_id' => $this->user_owner_id,
            'user_owner_name' => optional($this->userOwner)->name,
            'lottery_id' => $this->lottery_id,
            'lottery_name' => optional($this->lottery)->lottery_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
