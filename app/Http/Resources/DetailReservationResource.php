<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ReservationDetail",
 *     title="Reservation Detail",
 *     description="Detalle de un ítem dentro de una reservación",
 *     @OA\Property(property="id", type="integer", example=171, description="ID del detalle de la reservación"),
 *     @OA\Property(property="name", type="string", example="Producto 01", description="Nombre del producto o promoción"),
 *     @OA\Property(property="cant", type="integer", example=10, description="Cantidad solicitada"),
 *     @OA\Property(property="type", type="string", example="promocion", description="Tipo de ítem (producto o promoción)"),
 *     @OA\Property(property="precio", type="string", example="111.00", description="Precio unitario"),
 *     @OA\Property(property="status", type="string", example="Pendiente Pago", description="Estado del ítem en la reservación"),
 *     @OA\Property(property="reservation_id", type="integer", example=118, description="ID de la reservación a la que pertenece"),
 *     @OA\Property(property="promotion_id", type="integer", example=13, description="ID de la promoción (si aplica)"),
 *     @OA\Property(property="precio_total", type="string", example="1110.00", description="Precio total del ítem")
 * )
 */
class DetailReservationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id ?? null,
            'name'           => $this->name ?? null,
            'cant'           => $this->cant ?? null,
            'type'           => $this->type ?? null,
            'precio'         => $this->precio ?? null,
            'status'         => $this->status ?? null,
            'reservation_id' => $this->reservation_id ?? null,
            'promotion_id'   => $this->promotion_id ?? null,
            'promotion'   => new PromotionResource($this->promotion)  ?? null,
            'precio_total'   => $this->precio_total ?? null,
        ];
    }
}
