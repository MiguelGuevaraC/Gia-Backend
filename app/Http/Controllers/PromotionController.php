<?php
namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest\IndexPromotionRequest;
use App\Http\Requests\PromotionRequest\StorePromotionRequest;
use App\Http\Requests\PromotionRequest\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use App\Services\PromotionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/promotion",
     *     summary="Obtener información de promociones con filtros y ordenamiento",
     *     tags={"Promotion"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="precio", in="query", description="Filtrar por precio", required=false, @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="date_start", in="query", description="Filtrar por fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_end", in="query", description="Filtrar por fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="stock", in="query", description="Filtrar por stock", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", description="Filtrar por estado (activo, inactivo)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Filtrar desde esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Filtrar hasta esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Promociones", @OA\JsonContent(ref="#/components/schemas/Promotion")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */
    public function index(IndexPromotionRequest $request)
    {
        $query = Promotion::query();

        if ($request->filled('date_start')) {
            $filterDate = $request->date_start;
            $query->whereDate('date_start', '<=', $filterDate)
                ->whereDate('date_end', '>=', $filterDate);
        }

        $promotions = $this->getFilteredResults(
            $query,
            $request,
            Promotion::filters,
            Promotion::sorts,
            PromotionResource::class
        );

        if ($promotions instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $promotions = $promotions->getCollection();
        }

        $now = now();

        $promotions->each(function ($promotion) use ($now) {
            $promotion->recalculateStockPromotion();

            $inDateRange = $now->between($promotion->date_start, $promotion->date_end);
            $hasStock    = $promotion->stock_restante > 0;

            if ($inDateRange && $hasStock) {
                $promotion->status = 'Activo';
            } else {
                $promotion->status = 'Inactivo';
            }
        });

        return $promotions;
    }

    public function index_resumen(IndexPromotionRequest $request)
    {
        // Obtener fecha actual (sin hora)
        $today = Carbon::today();

        // Aplicar filtro: promociones activas el día de hoy
        $promotions = $this->getFilteredResults(
            Promotion::whereDate('date_start', '<=', $today)
                ->whereDate('date_end', '>=', $today),
            $request,
            Promotion::filters,
            Promotion::sorts,
            PromotionResource::class
        );

        return $promotions;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/promotion-app",
     *     summary="Obtener información de promociones con filtros y ordenamiento",
     *     tags={"Promotion"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="precio", in="query", description="Filtrar por precio", required=false, @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="date_start", in="query", description="Filtrar por fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_end", in="query", description="Filtrar por fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="stock", in="query", description="Filtrar por stock", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", description="Filtrar por estado (activo, inactivo)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Filtrar desde esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Filtrar hasta esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Promociones", @OA\JsonContent(ref="#/components/schemas/Promotion")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */
    public function index_app(IndexPromotionRequest $request)
    {
        // Obtener las promociones filtradas utilizando getFilteredResults
        $promotions = $this->getFilteredResults(
            Promotion::whereDate('date_start', '<=', now())
                ->whereDate('date_end', '>=', now()),
            // ->where('stock_restante', '>', 0),
            $request,
            Promotion::filters,
            Promotion::sorts,
            PromotionResource::class
        );

        if ($promotions instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $promotions = $promotions->getCollection();
        }
        $promotions->each(function ($promotion) {
            $promotion->recalculateStockPromotion();
        });
        return $promotions;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/promotion/{id}",
     *     summary="Obtener detalles de un promotion por ID",
     *     tags={"Promotion"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la empresa", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Promotion encontrada", @OA\JsonContent(ref="#/components/schemas/Promotion")),
     *     @OA\Response(response=404, description="Promocion No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Promocion No Encontrado")))
     * )
     */

    public function show($id)
    {

        $promotion = $this->promotionService->getPromotionById($id);

        if (! $promotion) {
            return response()->json([
                'error' => 'Promocion No Encontrado',
            ], 404);
        }

        return new PromotionResource($promotion);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/promotion",
     *     summary="Crear promotion",
     *     tags={"Promotion"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     * @OA\Schema(ref="#/components/schemas/PromotionRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Promotion creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Promotion")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un promotion asociado.")))
     * )
     */
    public function store(StorePromotionRequest $request)
    {
        $promotion = $this->promotionService->createPromotion($request->validated());
        return new PromotionResource($promotion);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/promotion/{id}",
     *     summary="Actualizar Promotion",
     *     tags={"Promotion"},
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
     * @OA\Schema(ref="#/components/schemas/PromotionRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Promotion actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Promotion")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un promotion asociado.")))
     * )
     */

    public function update(UpdatePromotionRequest $request, $id)
    {

        $validatedData = $request->validated();

        $promotion = $this->promotionService->getPromotionById($id);
        if (! $promotion) {
            return response()->json([
                'error' => 'Promocion No Encontrado',
            ], 404);
        }

        $updatedPromotion = $this->promotionService->updatePromotion($promotion, $validatedData);
        return new PromotionResource(Promotion::find($updatedPromotion->id));
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/promotion/{id}",
     *     summary="Eliminar promotion por ID",
     *     tags={"Promotion"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Promotion eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Promotion eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Promotion No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Promotion No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {

        $deleted = $this->promotionService->getPromotionById($id);
        if (! $deleted) {
            return response()->json([
                'error' => 'Promocion No Encontrado.',
            ], 404);
        }
        $deleted = $this->promotionService->destroyById($id);
        return response()->json([
            'message' => 'Promocion eliminado exitosamente',
        ], 200);
    }
}
