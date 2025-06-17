<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            //'code' => $this->code,
            'description' => $this->description,
            'barcode_path' => $this->barcode_path ? url(Storage::url($this->barcode_path)) : null,
            'qrcode_path' => $this->qrcode_path ? url(Storage::url($this->qrcode_path)) : null,
            'reservation_id' => $this->reservation_id,
            'lottery_ticket_id' => $this->lottery_ticket_id,
            'entry_id' => $this->entry_id,
            'created_at' => $this->created_at,
        ];
    }
}
