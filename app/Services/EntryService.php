<?php
namespace App\Services;

use App\Models\Entry;

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

    public function createEntry(array $data): Entry
    {
        $data['user_id'] = auth()->id(); // Obtiene el ID del usuario logueado
        $data['correlative'] = str_pad((int) Entry::where('event_id', $data['event_id'])->max('correlative') + 1, 8, '0', STR_PAD_LEFT);

        $entry = Entry::create([
            ...$data,
            'status' => 'Pendiente',
            'quantity' => '1',
            'entry_datetime' => now()
        ]);

        $resultado = $this->codeGeneratorService->generar('qrcode', [
            'description' => 'Entrada',
            'reservation_id' => null,
            'lottery_ticket_id' => null,
            'entry_id' => $entry->id,
        ]);

        return $entry;
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
