<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnvironmentAppResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="EnvironmentApp",
     *     title="EnvironmentApp",
     *     description="Environment Application model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", nullable=true, example="Sistema de Gestión"),
     *     @OA\Property(property="description", type="string", nullable=true, example="Descripción de la aplicación"),
     *     @OA\Property(property="route", type="string", example="/environment/123"),
     *     @OA\Property(property="status", type="string", example="Activo"),
     *     @OA\Property(property="server_id", type="integer", example=5),
     *     @OA\Property(property="company_id", type="integer", example=10),
     *     @OA\Property(
     *         property="stations",
     *         type="array",
     *         @OA\Items(type="object")
     *     )
     * )
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name ?? null,
            'description' => $this->description ?? null,
            'route'       => $this->route,
            'status'      => $this->status,
            'server_id'   => $this->server_id,
            'company_id'  => $this->company_id,
            'stations'    => $this->stations->isEmpty() ? [] : $this->stations,

        ];
    }

}
