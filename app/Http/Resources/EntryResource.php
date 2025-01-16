<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Entry",
     *     title="Entry",
     *     description="Entry model",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="entry_datetime", type="string", example="2025-01-16 14:30:00", description="Fecha y hora de entrada"),
     *     @OA\Property(property="code_pay", type="string", example="ABC123", description="Código de pago"),
     *     @OA\Property(property="quantity", type="string", example="10", description="Cantidad asociada al evento"),
     *     @OA\Property(property="status_pay", type="string", example="Pagado", description="Estado del pago"),
     *     @OA\Property(property="status_entry", type="string", example="Entrada Confirmada", description="Estado de la entrada"),
     *     @OA\Property(property="user_id", type="integer", example=42, description="ID del usuario"),
    *      @OA\Property(property="user", ref="#/components/schemas/User"),
     *     @OA\Property(property="person_id", type="integer", example=7, description="ID de la persona"),
     *     @OA\Property(property="person", type="object", ref="#/components/schemas/Person", description="Detalles de la persona asociada"),
     *     @OA\Property(property="event_id", type="integer", example=15, description="ID del evento relacionado"),
     *     @OA\Property(property="event", ref="#/components/schemas/Event")
     
     * )
     */

    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'entry_datetime' => $this->entry_datetime ?? 'Sin Fecha',
            'code_pay'       => $this->code_pay ?? 'Sin Código',
            'quantity'       => $this->quantity ?? 'Sin Cantidad',
            'status_pay'     => $this->status_pay ?? 'Sin Estado de Pago',
            'status_entry'   => $this->status_entry ?? 'Sin Estado de Entrada',
            'user_id'        => $this->user_id ?? 'Sin ID Usuario',
            'user'           => $this->user ? $this->user : 'Sin Usuario',
            'person_id'      => $this->person_id ?? 'Sin ID Persona',
            'person'         => $this->person ? new PersonResource($this->person) : 'Sin Persona',
            'event_id'      => $this->event_id ?? 'Sin ID Persona',
            'event'         => $this->event ? new EventResource($this->event) : 'Sin Evento',
        ];
    }

}
