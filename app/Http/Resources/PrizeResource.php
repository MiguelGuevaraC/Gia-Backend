<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Prize",
 *     title="Prize",
 *     description="Prize model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Gran Premio"),
 *     @OA\Property(property="description", type="string", example="Viaje a Cancún"),
 *     @OA\Property(property="route", type="string", example="/sorteo/navidad"),
 *     @OA\Property(property="lottery_ticket",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=10),
 *         @OA\Property(property="code_correlative", type="string", example="A001"),
 *         @OA\Property(property="reason", type="string", example="Por ser cliente frecuente"),
 *         @OA\Property(property="has_winner", type="boolean", example=true),
 *         @OA\Property(property="code", ref="#/components/schemas/Code")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-05T10:00:00Z")
 * )
 */
class PrizeResource extends JsonResource
{
    public function toArray($request)
    {
        $ticket = $this->lottery_ticket;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'route' => $this->route,
            'lottery_ticket' => $ticket ? [
                'id' => $ticket->id,
                'code_correlative' => $ticket->code_correlative,
                'reason' => $ticket->reason,
                'code' => $ticket->codes ? new CodeResource($ticket->codes) : null,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}
