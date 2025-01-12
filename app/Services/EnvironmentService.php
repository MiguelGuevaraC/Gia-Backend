<?php
namespace App\Services;

use App\Models\Environment;
use Illuminate\Support\Facades\Storage;

class EnvironmentService
{

    public function getEnvironmentById(int $id): ?Environment
    {
        return Environment::find($id);
    }

    public function createEnvironment(array $data): Environment
    {
        $environment = Environment::create($data);

        if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName = "{$environment->id}_{$timestamp}.{$extension}";
            $filePath = $data['route']->storeAs('companies', $fileName, 'public');
            $environment->update(['route' => Storage::url($filePath)]);
        }
        return $environment;
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
