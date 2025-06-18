<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CodeResource extends JsonResource
{
    public function toArray($request): array
    {
        $scansQuery = $this->scan_logs(); // debe ser una relación hasMany o similar
        $scansQuerytotal = $this->scan_logs()->count();
        $firstOk = $scansQuery->where('status', 'ok')->orderBy('id', 'asc')->first();
        $lastAttempt = $this->scan_logs()->orderBy('id', 'desc')->first();
        return [
            'id' => $this?->id,
            'description' => $this?->description,
            'barcode_path' => $this?->barcode_path ? url(Storage::url($this?->barcode_path)) : null,
            'qrcode_path' => $this?->qrcode_path ? url(Storage::url($this?->qrcode_path)) : null,
            'reservation_id' => $this?->reservation_id,
            'lottery_ticket_id' => $this?->lottery_ticket_id,
            'entry_id' => $this?->entry_id,
            'created_at' => $this?->created_at,

            // Datos de escaneo
            'total_scans' => $scansQuerytotal,
            'first_ok_scan' => $firstOk ? [
                'created_at' => $firstOk->created_at,
            ] : null,
            'last_scan_attempt' => $lastAttempt ? [
                'created_at' => $lastAttempt->created_at,
            ] : null,
        ];
    }
}
