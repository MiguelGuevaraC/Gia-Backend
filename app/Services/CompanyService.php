<?php
namespace App\Services;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Support\Facades\Http;

class CompanyService
{

    public function getCompanyById(int $id): ?Company
    {
        return Company::find($id);
    }

    public function createCompany(array $data): Company
    {
        return Company::create($data);
    }

    public function updateCompany($company, array $data)
    {
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
