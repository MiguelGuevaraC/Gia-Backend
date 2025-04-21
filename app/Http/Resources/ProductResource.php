<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Product",
     *     title="Product",
     *     description="Product model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Producto 1"),
     *     @OA\Property(property="description", type="string", example="Descripción del producto"),
     *     @OA\Property(property="precio", type="number", format="float", example=99.99),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="route", type="string", example="/product/1")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'precio' => $this->precio ?? null,
            'status' => $this->status ?? null,
            'route' => $this->route ?? null,
        ];
    }
}
