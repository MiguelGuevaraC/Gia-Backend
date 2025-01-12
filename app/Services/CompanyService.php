<?php
namespace App\Services;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CompanyService
{

    public function getCompanyById(int $id): ?Company
    {
        return Company::find($id);
    }

    public function createCompany(array $data): Company
    {
        $company = Company::create($data);
    
         if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName = "{$company->id}_{$timestamp}.{$extension}";
            $filePath = $data['route']->storeAs('companies', $fileName, 'public');
            $company->update(['route' => Storage::url($filePath)]);
        }
    
        return $company;
    }

    public function updateCompany(Company $company, array $data): Company
    {
        // Verificar si hay una nueva imagen en los datos
        if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            // Eliminar la imagen anterior si existe
            if ($company->route) {
                // Extraer la ruta relativa del archivo eliminando la URL pública
                $publicPath = env('APP_URL') . '/storage/';
                $relativePath = str_replace($publicPath, '', $company->route);
    
                // Borrar la imagen antigua si la ruta relativa existe
                Storage::disk('public')->delete($relativePath);
            }
    
            // Generar un nuevo nombre para la imagen
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName = "{$company->id}_{$timestamp}.{$extension}";
    
            // Guardar la nueva imagen
            $filePath = $data['route']->storeAs('companies', $fileName, 'public');
    
            // Actualizar el campo 'route' con la nueva URL completa
            $data['route'] = env('APP_URL') . Storage::url($filePath);
        }
    
        // Actualizar los datos de la compañía
        $company->update($data);
    
        return $company;
    }
    

    public function destroyById($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return false;
        }
        return $company->delete(); // Devuelve true si la eliminación fue exitosa
    }

   
    

}
