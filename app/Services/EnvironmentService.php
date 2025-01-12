<?php
namespace App\Services;

use App\Http\Resources\EnvironmentResource;
use App\Models\Environment;
use Illuminate\Support\Facades\Http;

class EnvironmentService
{

    public function getEnvironmentById(int $id): ?Environment
    {
        return Environment::find($id);
    }

    public function createEnvironment(array $data): Environment
    {
        return Environment::create($data);
    }

    public function updateEnvironment($Environment, array $data)
    {
        $Environment->update($data);
        return $Environment;
    }

    public function destroyById($id)
    {
        $Environment = Environment::find($id);

        if (!$Environment) {
            return false;
        }
        return $Environment->delete(); // Devuelve true si la eliminación fue exitosa
    }

   
    

}
