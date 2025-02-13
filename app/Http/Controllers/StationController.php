<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StationRequest\IndexStationRequest;
use App\Http\Requests\StationRequest\StoreStationRequest;
use App\Http\Requests\StationRequest\UpdateStationRequest;
use App\Http\Resources\StationResource;
use App\Models\Station;
use App\Services\StationService;

class StationController extends Controller
{

    protected $stationService;

    public function __construct(StationService $stationService)
    {
        $this->stationService = $stationService;
    }

/**
 * @OA\Get(
 *     path="/Gia-Backend/public/api/station",
 *     summary="Obtener información con filtros y ordenamiento",
 *     tags={"Station"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="type", in="query", description="Filtrar por tipo", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="status", in="query", description="Filtrar por estado", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="environment_id", in="query", description="ID del Ambiente", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="environment$name", in="query", description="Filtrar por nombre del entorno", required=false, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="Lista de Entornos", @OA\JsonContent(ref="#/components/schemas/Environment")),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(@OA\Property(property="error", type="string")))
 * )
 */

    public function index(IndexStationRequest $request)
    {
        Station::updateStatus();
        return $this->getFilteredResults(
            Station::class,
            $request,
            Station::filters,
            Station::sorts,
            StationResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/station/{id}",
     *     summary="Obtener detalles de un station por ID",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la Estación", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Station encontrada", @OA\JsonContent(ref="#/components/schemas/Station")),
     *     @OA\Response(response=404, description="Station No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Station No Encontrado")))
     * )
     */

    public function show($id)
    {

        $station = $this->stationService->getStationById($id);

        if (! $station) {
            return response()->json([
                'error' => 'Station No Encontrado',
            ], 404);
        }

        return new StationResource($station);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/station",
     *     summary="Crear station",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "type", "description", "status", "environment_id"},
     *                 @OA\Property(property="name", type="string", maxLength=255, example="Empresa X"),
     *                 @OA\Property(property="type", type="string", maxLength=50, example="Tipo A"),
     *                 @OA\Property(property="description", type="string", maxLength=50, example="Descripción breve"),
     *                 @OA\Property(property="status", type="boolean", example=true, description="Estado activo/inactivo"),
     *                 @OA\Property(property="route", type="string", format="binary", description="Subir archivo de imagen (jpg, jpeg, png, gif, máx. 2MB)", example="logo.jpg"),
     *                 @OA\Property(property="environment_id", type="integer", description="ID del entorno asociado", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Station creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Station")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Error en los datos enviados."))
     *     )
     * )
     */

    public function store(StoreStationRequest $request)
    {
        $station = $this->stationService->createStation($request->validated());
        return new StationResource($station);
    }
/**
 * @OA\Put(
 *     path="/Gia-Backend/public/api/station/{id}",
 *     summary="Actualizar Station",
 *     tags={"Station"},
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
 *             @OA\Schema(
 *                 type="object",
 *                 required={},
 *                 @OA\Property(property="name", type="string", maxLength=255, example="Empresa X"),
 *                 @OA\Property(property="type", type="string", maxLength=50, example="Tipo A"),
 *                 @OA\Property(property="description", type="string", maxLength=50, example="Descripción breve"),
 *                 @OA\Property(property="status", type="boolean", example=true, description="Estado activo/inactivo"),
 *                 @OA\Property(property="route", type="string", format="binary", description="Subir archivo de imagen actualizado (jpg, jpeg, png, gif, máx. 2MB)", example="logo_updated.jpg"),
 *                 @OA\Property(property="environment_id", type="integer", description="ID del entorno asociado", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Station actualizado exitosamente",
 *         @OA\JsonContent(ref="#/components/schemas/Station")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Error en los datos enviados."))
 *     )
 * )
 */
    public function update(UpdateStationRequest $request, $id)
    {

        $validatedData = $request->validated();

        $station = $this->stationService->getStationById($id);
        if (! $station) {
            return response()->json([
                'error' => 'Station No Encontrado',
            ], 404);
        }

        $updatedStation = $this->stationService->updateStation($station, $validatedData);
        return new StationResource($updatedStation);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/station/{id}",
     *     summary="Eliminar station por ID",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la estación que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Station eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Station eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Station No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Station No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->stationService->destroyById($id);

        if (! $deleted) {
            return response()->json([
                'error' => 'Station No Encontrado.',
            ], 404);
        }
        if ($deleted->reservations()->exists()) {
            return response()->json([
                'error' => 'Este elemento está vinculado a reservaciones.',
            ], 404);
        }

        return response()->json([
            'message' => 'Station eliminado exitosamente',
        ], 200);
    }
}
