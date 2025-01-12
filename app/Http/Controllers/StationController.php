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
     *     path="/gia-backend/public/api/station",
     *     summary="Obtener información con filtros y ordenamiento",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="ruc", in="query", description="Filtrar por ruc de la empresa", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="business_name", in="query", description="Filtrar razón social", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="address", in="query", description="Filtrar por dirección", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone", in="query", description="Filtrar por telefono", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", description="Filtrar por email", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Empresas", @OA\JsonContent(ref="#/components/schemas/Station")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */

    public function index(IndexStationRequest $request)
    {

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
     *     path="/gia-backend/public/api/station/{id}",
     *     summary="Obtener detalles de un station por ID",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la persona", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="persona encontrada", @OA\JsonContent(ref="#/components/schemas/Station")),
     *     @OA\Response(response=404, description="Station No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Station No Encontrado")))
     * )
     */

    public function show($id)
    {

        $station = $this->stationService->getStationById($id);

        if (!$station) {
            return response()->json([
                'error' => 'Station No Encontrado',
            ], 404);
        }

        return new StationResource($station);
    }

    /**
     * @OA\Post(
     *     path="/gia-backend/public/api/station",
     *     summary="Crear station",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "ruc", "business_name", "status"},
     *                 @OA\Property(property="name", type="string", example="Empresa X"),
     *                 @OA\Property(property="ruc", type="string", example="12345678901"),
     *                 @OA\Property(property="business_name", type="string", example="Razón Social S.A."),
     *                 @OA\Property(property="address", type="string", example="Calle Ficticia 123"),
     *                 @OA\Property(property="phone", type="string", example="987654321"),
     *                 @OA\Property(property="email", type="string", example="contacto@empresa.com"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="route", type="string", format="uri", description="Sube el logo de la empresa", example="logo.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Station creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Station")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un station asociado.")))
     * )
     */
    public function store(StoreStationRequest $request)
    {
        $station = $this->stationService->createStation($request->validated());
        return new StationResource($station);
    }

    /**
     * @OA\Put(
     *     path="/gia-backend/public/api/station/{id}",
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
     *                 @OA\Property(property="name", type="string", example="Empresa X"),
     *                 @OA\Property(property="ruc", type="string", example="12345678901"),
     *                 @OA\Property(property="business_name", type="string", example="Razón Social S.A."),
     *                 @OA\Property(property="address", type="string", example="Calle Ficticia 123"),
     *                 @OA\Property(property="phone", type="string", example="987654321"),
     *                 @OA\Property(property="email", type="string", example="contacto@empresa.com"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="route", type="string", format="uri", description="Sube el logo de la empresa", example="logo_updated.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Station actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Station")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un station asociado.")))
     * )
     */

    public function update(UpdateStationRequest $request, $id)
    {

        $validatedData = $request->validated();

        $station = $this->stationService->getStationById($id);
        if (!$station) {
            return response()->json([
                'error' => 'Station No Encontrado',
            ], 404);
        }

        $updatedStation = $this->stationService->updateStation($station, $validatedData);
        return new StationResource($updatedStation);
    }

    /**
     * @OA\Delete(
     *     path="/gia-backend/public/api/station/{id}",
     *     summary="Eliminar station por ID",
     *     tags={"Station"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Station eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Station eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Station No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Station No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->stationService->destroyById($id);

        if (!$deleted) {
            return response()->json([
                'error' => 'Station No Encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Station eliminado exitosamente',
        ], 200);
    }
}
