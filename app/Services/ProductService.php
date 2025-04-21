<?php
namespace App\Services;

use App\Models\Product;

class ProductService
{

    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }
    public function getProductById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function createProduct(array $data): Product
    {
        $data['status']='Activo';
        $Product = Product::create($data);
        $this->commonService->store_photo($data, $Product, 'Products');
        return $Product;
    }

    public function updateProduct($Product, array $data)
    {
        if (isset($data['route'])) {
            $data['route'] = $this->commonService->update_photo($data, $Product, 'Products');
        }
        $Product->update($data);
        return $Product;
    }

    public function updateProductstatus($Product, string $status)
    {
        $Product->update(["status" => $status]);
        return $Product;
    }

    public function destroyById($id)
    {
        $Product = Product::find($id);

        if (! $Product) {
            return false;
        }
        return $Product->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
