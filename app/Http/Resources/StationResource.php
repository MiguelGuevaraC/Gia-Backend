<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Station",
     *     title="Station",
     *     description="Station model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Station A"),
     *     @OA\Property(property="description", type="string", example="Description"),
     *     @OA\Property(property="type", type="string", example="Monitoring"),
     *     @OA\Property(property="status", type="boolean", example=true),
     *     @OA\Property(property="server_id", type="integer", example=15),
      *     @OA\Property(property="date_reservation", type="integer", example="22 de Noviembre dell 2024"),
     *     @OA\Property(property="environment_id", type="integer", example=5),
     *     @OA\Property(property="environment", ref="#/components/schemas/Environment")
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? 'Sin Nombre',
            'description' => $this->description ?? 'Sin Descripción',
            'type' => $this->type ?? 'Sin Tipo',
            'status' => $this->status ?? 'Sin Estado',
            'server_id' => $this->server_id,
            'date_reservation' => 'No hay reserva existente para esta mesa.',
            'environment_id' => $this->environment_id,
            'environment' => $this->environment ? new EnvironmentResource($this->environment) : 'Sin Ambiente',
        ];
    }

}
