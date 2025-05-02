<?php
namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest\IndexSettingRequest;
use App\Http\Requests\SettingRequest\UpdateSettingDescountRequest;
use App\Http\Requests\SettingRequest\UpdateSettingTimeReservationRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/setting",
     *     summary="Listar configuraciones (Settings)",
     *     tags={"Setting"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="type", in="query", description="Filtrar por tipo", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filtrar por estado", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Lista de configuraciones", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Setting"))),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(@OA\Property(property="error", type="string")))
     * )
     */
    public function index(IndexSettingRequest $request)
    {
        return $this->getFilteredResults(
            Setting::class,
            $request,
            Setting::filters,
            Setting::sorts,
            SettingResource::class
        );
    }

    /**
     * @OA\Put(
     *     path="/Gia-Backend/public/api/setting-time-reserva",
     *     summary="Actualizar tiempo de reserva",
     *     tags={"Setting"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="integer", example=5, description="Cantidad de minutos para expiración")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Configuración actualizada", @OA\JsonContent(ref="#/components/schemas/Setting")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string")))
     * )
     */
    public function update_time_reservation(UpdateSettingTimeReservationRequest $request)
    {
        return $this->updateSettingById($request->validated(), 1);
    }

    /**
     * @OA\Put(
     *     path="/Gia-Backend/public/api/setting-descount-percent",
     *     summary="Actualizar porcentaje de descuento",
     *     tags={"Setting"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=10.5, description="Porcentaje de descuento")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Configuración actualizada", @OA\JsonContent(ref="#/components/schemas/Setting")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string")))
     * )
     */
    public function update_descount_percent(UpdateSettingDescountRequest $request)
    {
        return $this->updateSettingById($request->validated(), 2);
    }

    private function updateSettingById(array $data, int $id)
    {
        $setting = Setting::findOrFail($id);
        $updated = $this->settingService->updateSetting($setting, $data);
        return new SettingResource($updated);
    }

}
