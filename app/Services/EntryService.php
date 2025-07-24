<?php
namespace App\Services;

use App\Models\Entry;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EntryService
{
    protected $codeGeneratorService;

    public function __construct(CodeGeneratorService $codeGeneratorService)
    {
        $this->codeGeneratorService = $codeGeneratorService;
    }
    public function getEntryById(int $id): ?Entry
    {
        return Entry::find($id);
    }


    public function createEntry(array $data): Collection
    {
        $data['user_id'] = auth()->id();

        // Crear evento diario si no hay uno existente y event_id es null
        if (empty($data['event_id'])) {
            $entryDate = !empty($data['entry_daily_date'])
                ? Carbon::parse($data['entry_daily_date'])
                : now();

            $dayStart = $entryDate->copy()->startOfDay();
            $dayEnd = $entryDate->copy()->endOfDay();
            $companyId = $data['company_id'];

            $evento = Event::where('is_daily_event', '1')
                ->whereBetween('event_datetime', [$dayStart, $dayEnd])
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->first();

            if (!$evento) {
                $data['event_id'] = Event::create([
                    'name' => 'Evento ' . ucfirst($entryDate->translatedFormat('l d/F/Y')),
                    'event_datetime' => $dayEnd,
                    'company_id' => $companyId,
                    'is_daily_event' => '1',
                ])->id;
            } else {
                $data['event_id'] = $evento->id;
            }
        }

        $lastCorrelative = (int) Entry::where('event_id', $data['event_id'])->max('correlative') ?? 0;

        return collect(range(1, $data['quantity']))->map(function ($i) use ($data, $lastCorrelative) {
            $correlative = str_pad($lastCorrelative + $i, 8, '0', STR_PAD_LEFT);

            $entry = Entry::create([
                ...$data,
                'correlative' => $correlative,
                'status' => 'Pendiente',
                'entry_datetime' => now(),
            ]);

            $this->codeGeneratorService->generar('qrcode', [
                'description' => 'Entrada',
                'reservation_id' => null,
                'lottery_ticket_id' => null,
                'entry_id' => $entry->id,
            ]);

            return $entry;
        });
    }



    public function updateEntry(Entry $entry, array $data): Entry
    {
        $data = array_intersect_key($data, $entry->getAttributes());
        $entry->update($data);

        return $entry;
    }

    public function destroyById($id)
    {
        $Entry = Entry::find($id);

        if (!$Entry) {
            return false;
        }
        return $Entry->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
