<?php
namespace App\Services;

use App\Models\Company;

class CompanyService
{

    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function getCompanyById(int $id): ?Company
    {
        return Company::find($id);
    }

    public function createCompany(array $data): Company
    {
        $company = Company::create($data);
        $this->commonService->store_photo($data, $company, 'companies');
        return $company;
    }

    public function updateCompany(Company $company, array $data): Company
    {

        $data['route'] = $this->commonService->update_photo($data, $company, 'companies');
        $company->update($data);
        return $company;
    }

    public function destroyById($id)
    {
        $company = Company::find($id);

        if (! $company) {
            return false;
        }
        if ($company->environments()->exists()) {
            return 'No se puede eliminar porque tiene Ambientes relacionados.';
        }
        return $company->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
