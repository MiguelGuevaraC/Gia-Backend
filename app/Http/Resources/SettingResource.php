<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{

/**
 * @OA\Schema(
 *     schema="Setting",
 *     title="Setting",
 *     description="Model representing a Setting",
 *     required={"id", "name", "description", "amount", "created_at"},
 *     @OA\Property(property="id", type="integer", description="Setting ID"),
 *     @OA\Property(property="name", type="string", description="Name of the Setting"),
 *     @OA\Property(property="description", type="string", description="Description of the Setting"),
 *     @OA\Property(property="amount", type="number", format="float", description="Amount associated with the Setting"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation date of the Setting")
 * )
 */

    public function toArray($request): array
    {
        return [
            'id'          => $this->id ?? null,
            'name'        => $this->name ?? null,
            'description' => $this->description ?? null,
            'amount'      => $this->amount ?? null,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
