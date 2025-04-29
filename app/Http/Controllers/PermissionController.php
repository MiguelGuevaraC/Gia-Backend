<?php
namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest\IndexPermissionRequest;
use App\Http\Requests\PermissionRequest\StorePermissionRequest;
use App\Http\Requests\PermissionRequest\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\PermissionService;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

/**
 * @OA\Get(
 *     path="/Gia-Backend/public/api/permission",
 *     summary="Obtener información con filtros y ordenamiento",
 *     tags={"Permission"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="type", in="query", description="Filtrar por tipo", required=false, @OA\Schema(type="string")),

 *     @OA\Parameter(name="status", in="query", description="Filtrar por estado", required=false, @OA\Schema(type="string")),

 *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Response(response=200, description="Lista de Entornos", @OA\JsonContent(ref="#/components/schemas/Permission")),
 *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(@OA\Property(property="error", type="string")))
 * )
 */

    public function index(IndexPermissionRequest $request)
    {

        return $this->getFilteredResults(
            Permission::class,
            $request,
            Permission::filters,
            Permission::sorts,
            PermissionResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/permission/{id}",
     *     summary="Obtener detalles de un permission por ID",
     *     tags={"Permission"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de Permission", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Permission encontrada", @OA\JsonContent(ref="#/components/schemas/Permission")),
     *     @OA\Response(response=404, description="Permission No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Permission No Encontrado")))
     * )
     */

    public function show($id)
    {

        $permission = $this->permissionService->getPermissionById($id);

        if (! $permission) {
            return response()->json([
                'error' => 'Permiso No Encontrado',
            ], 404);
        }

        return new PermissionResource($permission);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/permission",
     *     summary="Crear un permissiono",
     *     tags={"Permission"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "permission_datetime"},
     *                 @OA\Property(property="name", type="string", maxLength=255, description="Nombre del permissiono"),
     *                 @OA\Property(property="permission_datetime", type="string", format="date", description="Fecha y hora del permissiono (YYYY-MM-DD)"),
     *                 @OA\Property(property="comment", type="string", maxLength=1000, description="Comentario sobre el permissiono", nullable=true),

     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permiso creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Permission")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al crear el permissiono")))
     * )
     */

    public function store(StorePermissionRequest $request)
    {
        $data           = $request->validated();
        $data['status'] = 'Reservado';
        $permission     = $this->permissionService->createPermission($data);
        return new PermissionResource($permission);
    }

/**
 * @OA\Put(
 *     path="/Gia-Backend/public/api/permission/{id}",
 *     summary="Editar un permissiono",
 *     tags={"Permission"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del permissiono a editar",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "permission_datetime"},
 *                 @OA\Property(property="name", type="string", maxLength=255, description="Nombre del permissiono"),
 *                 @OA\Property(property="permission_datetime", type="string", format="date", description="Fecha y hora del permissiono (YYYY-MM-DD)"),
 *                 @OA\Property(property="comment", type="string", maxLength=1000, description="Comentario sobre el permissiono", nullable=true),
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Permiso actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Permission")),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al actualizar el permissiono"))),
 *     @OA\Response(response=404, description="Permiso no encontrado", @OA\JsonContent(@OA\Property(property="error", type="string", example="El permissiono no existe")))
 * )
 */

    public function update(UpdatePermissionRequest $request, $id)
    {
        $validatedData = $request->validated();

        $permission = $this->permissionService->getPermissionById($id);
        if (! $permission) {
            return response()->json([
                'error' => 'Permiso No Encontrado',
            ], 404);
        }

        $updatedPermission = $this->permissionService->updatePermission($permission, $validatedData);
        return new PermissionResource($updatedPermission);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/permission/{id}",
     *     summary="Eliminar permission por ID",
     *     tags={"Permission"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID del permissiono que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Permission eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Permission eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Permission No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Permission No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $permission = $this->permissionService->getPermissionById($id);
        if (! $permission) {
            return response()->json(['error' => 'Permiso no encontrado.'], 404);
        }
        if ($permission->roles()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el permiso porque está asignado a uno o más roles.',
            ], 422);
        }
        $this->permissionService->destroyById($id);
        return response()->json(['message' => 'Permiso eliminado exitosamente.'], 200);
    }

}
