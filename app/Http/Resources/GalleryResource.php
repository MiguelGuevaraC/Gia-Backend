<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Gallery",
 *     title="Gallery",
 *     description="Gallery model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name_table", type="string", example="gallery_table"),
 *     @OA\Property(property="route", type="string", example="/images/gallery.jpg"),
 *     @OA\Property(property="company_id", type="integer", example=10),
 *     @OA\Property(property="company_name", type="string", example="Empresa XYZ"),
 *     @OA\Property(property="user_created_id", type="integer", example=5),
 *     @OA\Property(property="user_created_name", type="string", example="Juan Pérez"),
 * )
 */
class GalleryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'name_image'        => $this->name_image,
            'route'             => $this->route,
            'company_id'        => $this->company_id,
            'company_name'      => $this?->company?->business_name,
            'user_created_id'   => $this->user_created_id,
            'user_created_name' => $this?->user_created?->name,
        ];
    }
}
