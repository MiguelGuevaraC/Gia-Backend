<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyAppResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="CompanyApp",
     *     title="CompanyApp",
     *     description="Company model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="ruc", type="string", nullable=true, example="20123456789"),
     *     @OA\Property(property="business_name", type="string", nullable=true, example="Tech Solutions S.A."),
     *     @OA\Property(property="address", type="string", nullable=true, example="Av. Principal 123, Lima"),
     *     @OA\Property(property="phone", type="string", nullable=true, example="987654321"),
     *     @OA\Property(property="email", type="string", nullable=true, example="info@techsolutions.com"),
     *     @OA\Property(property="route", type="string", nullable=true, example="/company/123"),
     *     @OA\Property(property="status", type="string", nullable=true, example="Activo"),
     *     @OA\Property(
     *         property="environments",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/EnvironmentApp")
     *     )
     * )
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'ruc'           => $this->ruc ?? null,
            'business_name' => $this->business_name ?? null,
            'address'       => $this->address ?? null,
            'phone'         => $this->phone ?? null,
            'email'         => $this->email ?? null,
            'route'         => $this->route ?? null,
            'status'        => $this->status ?? null,
            'environments'  => $this->environments->isEmpty() ? [] : EnvironmentAppResource::collection($this->environments),
            'events'        => $this->upcomingEvents->isEmpty() ? [] : EventAppResource::collection($this->upcomingEvents),

        ];
    }

}
