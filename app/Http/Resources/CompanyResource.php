<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Company",
     *     title="Company",
     *     description="Company model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="ruc", type="string", example="20123456789"),
     *     @OA\Property(property="business_name", type="string", example="Tech Solutions S.A."),
     *     @OA\Property(property="address", type="string", example="Av. Principal 123, Lima"),
     *     @OA\Property(property="phone", type="string", example="987654321"),
     *     @OA\Property(property="email", type="string", example="info@techsolutions.com"),
     *     @OA\Property(property="route", type="string", example="/company/123")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ruc' => $this->ruc ?? null,
            'business_name' => $this->business_name ?? null,
            'address' => $this->address ?? null,
            'phone' => $this->phone ?? null,
            'email' => $this->email ?? null,
            'route' => $this->route ?? null,
            'status' => $this->status ?? null,
        ];
    }

}
