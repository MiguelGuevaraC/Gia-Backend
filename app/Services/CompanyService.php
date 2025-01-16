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
        $ruta="https://develop.garzasoft.com/Gia-Backend/public";
        if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            if ($company->route) {
                $publicPath = $ruta . '/storage/';
                $relativePath = str_replace($publicPath, '', $company->route);
                Storage::disk('public')->delete($relativePath);
            }
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName = "{$company->id}_{$timestamp}.{$extension}";
            $filePath = $data['route']->storeAs('companies', $fileName, 'public');
            $data['route'] = $ruta . Storage::url($filePath);
        }
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
