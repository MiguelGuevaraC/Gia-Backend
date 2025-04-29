<?php
namespace App\Services;

use App\Models\Person;

class PersonService
{

    public function getPersonById(int $id): ?Person
    {
        return Person::find($id);
    }

    public function createPerson(array $data): Person
    {
        return Person::create($data);
    }

    public function updatePerson($person, array $data)
    {
        $filteredData = array_intersect_key($data, $person->getAttributes());
        $person->update($filteredData);
        return $person;
    }

    public function destroyById($id)
    {
        $Person = Person::find($id);

        if (! $Person) {
            return false;
        }
        return $Person->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
