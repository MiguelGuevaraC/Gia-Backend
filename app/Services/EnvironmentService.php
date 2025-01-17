<?php
namespace App\Services;

use App\Models\Environment;
use Illuminate\Support\Facades\Storage;

class EnvironmentService
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }
    public function getEnvironmentById(int $id): ?Environment
    {
        return Environment::find($id);
    }

    public function createEnvironment(array $data): Environment
    {
        $environment = Environment::create($data);
        $this->commonService->store_photo($data, $environment, 'environments');

        return $environment;
    }

    public function updateEnvironment(Environment $environment, array $data): Environment
    {
        $data['route'] = $this->commonService->update_photo($data, $environment, 'environments');
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
