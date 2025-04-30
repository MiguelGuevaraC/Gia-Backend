<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest\IndexProductRequest;
use App\Http\Requests\ProductRequest\StoreProductRequest;
use App\Http\Requests\ProductRequest\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/product",
     *     summary="Obtener información con filtros y ordenamiento",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="precio", in="query", description="Filtrar por precio", required=false, @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="status", in="query", description="Filtrar por estado", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de productos", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */

    public function index(IndexProductRequest $request)
    {

        return $this->getFilteredResults(
            Product::class,
            $request,
            Product::filters,
            Product::sorts,
            ProductResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/product/{id}",
     *     summary="Obtener detalles de un product por ID",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la empresa", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Product encontrada", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="Producto No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Producto No Encontrado")))
     * )
     */

    public function show($id)
    {

        $product = $this->productService->getProductById($id);

        if (! $product) {
            return response()->json([
                'error' => 'Producto No Encontrado',
            ], 404);
        }

        return new ProductResource($product);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/product",
     *     summary="Crear product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     * @OA\Schema(ref="#/components/schemas/ProductRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un product asociado.")))
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return new ProductResource($product);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/product/{id}",
     *     summary="Actualizar Product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa que se desea actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     * @OA\Schema(ref="#/components/schemas/ProductRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un product asociado.")))
     * )
     */

    public function update(UpdateProductRequest $request, $id)
    {

        $validatedData = $request->validated();

        $product = $this->productService->getProductById($id);
        if (! $product) {
            return response()->json([
                'error' => 'Producto No Encontrado',
            ], 404);
        }

        $updatedProduct = $this->productService->updateProduct($product, $validatedData);
        return new ProductResource($updatedProduct);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/product/{id}",
     *     summary="Eliminar product por ID",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Product eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Product eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Product No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Product No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->productService->getProductById($id);

        if (! $deleted) {
            return response()->json([
                'error' => 'Producto No Encontrado.',
            ], 404);
        }
        $deleted = $this->productService->destroyById($id);

        return response()->json([
            'message' => 'Producto eliminado exitosamente',
        ], 200);
    }
}
