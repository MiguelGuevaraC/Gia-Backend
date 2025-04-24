<?php
namespace App\Services;

use App\Models\Promotion;

class PromotionService
{

    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }
    public function getPromotionById(int $id): ?Promotion
    {
        return Promotion::find($id);
    }

    public function createPromotion(array $data): Promotion
    {
        $data['status']='Activo';
        $data['stock_restante']=$data['stock'];
        $Promotion = Promotion::create($data);
        $this->commonService->store_photo($data, $Promotion, 'Promotions');
        return $Promotion;
    }

    public function updatePromotion($Promotion, array $data)
    {
        if (isset($data['route'])) {
            $data['route'] = $this->commonService->update_photo($data, $Promotion, 'Promotions');
        }
        $filteredData = array_intersect_key($data, $Promotion->getAttributes());
        $Promotion->update($filteredData);
        return $Promotion;
    }

    public function updatePromotionstatus($Promotion, string $status)
    {
        $Promotion->update(["status" => $status]);
        return $Promotion;
    }

    public function destroyById($id)
    {
        $Promotion = Promotion::find($id);

        if (! $Promotion) {
            return false;
        }
        return $Promotion->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
