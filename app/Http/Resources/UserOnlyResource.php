<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserOnlyResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="User1",
     *     title="User Only",
     *     description="User model",
     *     @OA\Property( property="id", type="integer", example="1" ),
     *     @OA\Property( property="email", type="string", example="miguel@gmail.com" ),

     *     @OA\Property(property="person_id",type="integer",description="Person Id", example="1"),
     *     @OA\Property(property="person", ref="#/components/schemas/Person"),
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? 'Sin Nombre',
            'username' => $this->username ?? 'Sin Correo',
            'person_id' => $this->person_id ?? 'Sin Persona ID',
            'rol_id' => $this->rol_id ?? 'Sin Tipo Usuario ID',
            'person' => $this->person ? new PersonResource($this->person) : 'Sin Persona',
        ];

    }
}
