<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnvironmentResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Environment",
     *     title="Environment",
     *     description="Environment model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Production"),
     *     @OA\Property(property="description", type="string", example="Primary production environment"),
     *     @OA\Property(property="route", type="string", example="/environment/production"),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="server_id", type="integer", example=10),
     *     @OA\Property(property="company_id", type="integer", example=3),
     *     @OA\Property(property="company", ref="#/components/schemas/Company")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? 'Sin Nombre',
            'description' => $this->description ?? 'Sin Descripción',
            'route' => $this->route,
            'status' => $this->status,
            'server_id' => $this->server_id,
            'company_id' => $this->company_id,
            'company' => $this->company ? new CompanyResource($this->company) : 'Sin Empresa',
        ];
    }

}
