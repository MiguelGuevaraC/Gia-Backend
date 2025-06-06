<?php
namespace App\Services;

use App\Models\Entry;

class EntryService
{

    public function getEntryById(int $id): ?Entry
    {
        return Entry::find($id);
    }

    public function createEntry(array $data): Entry
    {
        $data['user_id'] = auth()->id(); // Obtiene el ID del usuario logueado

        $event = Entry::create($data);

        return $event;
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

        if (! $Entry) {
            return false;
        }
        return $Entry->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
