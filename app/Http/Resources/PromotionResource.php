<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Promotion",
     *     title="Promotion",
     *     description="Promotion model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Promo Verano"),
     *     @OA\Property(property="description", type="string", example="20% de descuento en todos los productos"),
     *     @OA\Property(property="date_start", type="string", format="date", example="2024-08-01"),
     *     @OA\Property(property="date_end", type="string", format="date", example="2024-08-31"),
     *     @OA\Property(property="stock", type="integer", example=100),
     *     @OA\Property(property="route", type="string", example="/promotions/1"),
     *     @OA\Property(property="product_id", type="integer", example=5),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-15T12:34:56Z")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'precio' => $this->precio ?? null,
            'date_start' => $this->date_start ?? null,
            'date_end' => $this->date_end ?? null,
            'stock' => $this->stock ?? null,
            'status' => $this->status ?? null,
            'route' => $this?->product?->route ?? null,
            
            'product_id' => $this->product_id ?? null,
            'created_at' => $this->created_at ?? null,
        ];
    }
}
