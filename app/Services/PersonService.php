<?php
namespace App\Services;

use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Support\Facades\Http;

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

    public function updatePerson($Person, array $data)
    {
        $Person->update($data);
        return $Person;
    }

    public function destroyById($id)
    {
        $Person = Person::find($id);

        if (!$Person) {
            return false;
        }
        return $Person->delete(); // Devuelve true si la eliminación fue exitosa
    }

   
    

}
