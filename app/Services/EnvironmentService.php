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

    public function updateEnvironment(Environment $environment, array $data): Environment
    {
        // Verificar si hay una nueva imagen en los datos
        if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            // Eliminar la imagen anterior si existe
            if ($environment->route) {
                // Extraer la ruta relativa del archivo eliminando la URL pública
                $publicPath = env('APP_URL') . '/storage/';
                $relativePath = str_replace($publicPath, '', $environment->route);
    
                // Borrar la imagen antigua si la ruta relativa existe
                Storage::disk('public')->delete($relativePath);
            }
    
            // Generar un nuevo nombre para la imagen
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName = "{$environment->id}_{$timestamp}.{$extension}";
    
            // Guardar la nueva imagen
            $filePath = $data['route']->storeAs('environments', $fileName, 'public');
    
            // Actualizar el campo 'route' con la nueva URL completa
            $data['route'] = env('APP_URL') . Storage::url($filePath);
        }
    
        // Actualizar los datos del ambiente
        $environment->update($data);
    
        return $environment;
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
