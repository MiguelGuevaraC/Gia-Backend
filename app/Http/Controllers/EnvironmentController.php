<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnvironmentRequest\IndexEnvironmentRequest;
use App\Http\Requests\EnvironmentRequest\StoreEnvironmentRequest;
use App\Http\Requests\EnvironmentRequest\UpdateEnvironmentRequest;
use App\Http\Resources\EnvironmentResource;
use App\Models\Environment;
use App\Services\EnvironmentService;

class EnvironmentController extends Controller
{

    protected $environmentService;

    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

/**
 * @OA\Get(
 *     path="/gia-backend/public/api/environment",
 *     summary="Obtener información con filtros y ordenamiento",
 *     tags={"Environment"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="route", in="query", description="Filtrar por ruta", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="status", in="query", description="Filtrar por estado", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="company$business_name", in="query", description="Filtrar por nombre de empresa", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Response(response=200, description="Lista de Entornos", @OA\JsonContent(ref="#/components/schemas/Environment")),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(@OA\Property(property="error", type="string")))
 * )
 */

    public function index(IndexEnvironmentRequest $request)
    {

        return $this->getFilteredResults(
            Environment::class,
            $request,
            Environment::filters,
            Environment::sorts,
            EnvironmentResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/gia-backend/public/api/environment/{id}",
     *     summary="Obtener detalles de un environment por ID",
     *     tags={"Environment"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la persona", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="persona encontrada", @OA\JsonContent(ref="#/components/schemas/Environment")),
     *     @OA\Response(response=404, description="Environment No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Environment No Encontrado")))
     * )
     */

    public function show($id)
    {

        $environment = $this->environmentService->getEnvironmentById($id);

        if (!$environment) {
            return response()->json([
                'error' => 'Environment No Encontrado',
            ], 404);
        }

        return new EnvironmentResource($environment);
    }

    /**
     * @OA\Post(
     *     path="/gia-backend/public/api/environment",
     *     summary="Crear Environment",
     *     tags={"Environment"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "company_id"},
     *                 @OA\Property(property="name", type="string", description="Nombre del entorno"),
     *                 @OA\Property(property="description", type="string", description="Descripción del entorno"),
     *                 @OA\Property(property="route", type="string", description="Archivo de la imagen", format="binary"),
     *                 @OA\Property(property="status", type="boolean", description="Estado del entorno"),
     *                 @OA\Property(property="company_id", type="integer", description="ID de la empresa", example=1),
     
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Environment creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Environment")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al crear el environment")))
     * )
     */

    public function store(StoreEnvironmentRequest $request)
    {
        $environment = $this->environmentService->createEnvironment($request->validated());
        return new EnvironmentResource($environment);
    }

/**
 * @OA\Put(
 *     path="/gia-backend/public/api/environment/{id}",
 *     summary="Actualizar Environment por ID",
 *     tags={"Environment"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="id", in="path", description="ID del entorno", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(property="name", type="string", description="Nombre del entorno", example="Entorno Ejemplo"),
 *                 @OA\Property(property="description", type="string", description="Descripción del entorno", example="Descripción del entorno"),
 *                 @OA\Property(property="route", type="string", description="Archivo de la imagen", format="binary", nullable=true),
 *                 @OA\Property(property="status", type="boolean", description="Estado del entorno", example=true),
 *                 @OA\Property(property="company_id", type="integer", description="ID de la empresa", example=1),
 
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Environment actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Environment")),
 *     @OA\Response(response=404, description="Environment No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Environment No Encontrado"))),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Error al actualizar el environment")))
 * )
 */

    public function update(UpdateEnvironmentRequest $request, $id)
    {

        $validatedData = $request->validated();

        $environment = $this->environmentService->getEnvironmentById($id);
        if (!$environment) {
            return response()->json([
                'error' => 'Environment No Encontrado',
            ], 404);
        }

        $updatedEnvironment = $this->environmentService->updateEnvironment($environment, $validatedData);
        return new EnvironmentResource($updatedEnvironment);
    }

    /**
     * @OA\Delete(
     *     path="/gia-backend/public/api/environment/{id}",
     *     summary="Eliminar environment por ID",
     *     tags={"Environment"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Environment eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Environment eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Environment No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Environment No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->environmentService->destroyById($id);

        if (!$deleted) {
            return response()->json([
                'error' => 'Environment No Encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Environment eliminado exitosamente',
        ], 200);
    }
}
