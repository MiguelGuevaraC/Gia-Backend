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

    public function updateEntry(Entry $environment, array $data): Entry
    {

        $environment->update($data);

        return $environment;
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
