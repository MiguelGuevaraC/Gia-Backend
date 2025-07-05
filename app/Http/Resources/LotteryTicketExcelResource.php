<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LotteryTicketExcelResource extends JsonResource
{
    public function toArray($request)
    {
        $person = optional($this->userOwner)->person;

        $fullName = collect([
            $person->names ?? null,
            $person->father_surname ?? null,
            $person->mother_surname ?? null,
            $person->business_name ?? null,
        ])->filter()->implode(' ') ?: 'Sin nombre';

        $contact = collect([
            $person->address ?? null,
            $person->phone ?? null,
            $person->email ?? null,
        ])->filter()->implode(' / ') ?: 'Sin contacto';

        $code = $this->codes;
        $url = $code?->qrcode_path
            ? url(Storage::url($code->qrcode_path))
            : ($code?->barcode_path
                ? url(Storage::url($code->barcode_path))
                : null);

        return [
            'Número de Ticket' => $this->code_correlative,
            'Cliente' => $fullName,
            'Contacto' => $contact,
            'QR' => $url
                ? '=HYPERLINK("' . $url . '", "🖼️ Ver QR")'
                : '-',
            'Fecha de Emisión' => optional($this->created_at)?->format('d/m/Y H:i'),
        ];
    }
}
