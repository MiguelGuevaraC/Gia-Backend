<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
   /**
 * @OA\Schema(
 *     schema="Person",
 *     title="Person",
 *     description="Person model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type_document", type="string", example="DNI"),
 *     @OA\Property(property="type_person", type="string", example="Individual"),
 *     @OA\Property(property="number_document", type="string", example="12345678"),
 *     @OA\Property(property="names", type="string", example="Juan"),
 *     @OA\Property(property="father_surname", type="string", example="Perez"),
 *     @OA\Property(property="mother_surname", type="string", example="Lopez"),
 *     @OA\Property(property="business_name", type="string", example="Perez & Co."),
 *     @OA\Property(property="address", type="string", example="Av. Siempre Viva 123"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="email", type="string", example="juanperez@example.com"),
 *     @OA\Property(property="occupation", type="string", example="Engineer"),
 *     @OA\Property(property="state", type="boolean", example=true),
 *     @OA\Property(property="server_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T12:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example=null)
 * )
 */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type_document' => $this->type_document,
            'type_person' => $this->type_person,
            'number_document' => $this->number_document,
            'names' => $this->names,
            'father_surname' => $this->father_surname,
            'mother_surname' => $this->mother_surname,
            'business_name' => $this->business_name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'occupation' => $this->occupation,
            'state' => $this->state,

        ];
    }
    
}
