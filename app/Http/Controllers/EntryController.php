<?php
namespace App\Http\Controllers;

use App\Http\Requests\EntryRequest\IndexEntryRequest;
use App\Http\Requests\EntryRequest\StoreEntryRequest;
use App\Http\Requests\EntryRequest\UpdateEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Services\AuditLogService;
use App\Services\CulquiService;
use App\Services\EntryService;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    protected $entryService;
    protected $culquiService;

    public function __construct(EntryService $entryService, CulquiService $culquiService)
    {
        $this->entryService = $entryService;
        $this->culquiService = $culquiService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/entry",
     *     summary="Obtener información con filtros y ordenamiento",
     *     tags={"Entry"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="entry_datetime", in="query", description="Filtrar por fecha de entrada", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="code_pay", in="query", description="Filtrar por código de pago", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="quantity", in="query", description="Filtrar por cantidad", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status_pay", in="query", description="Filtrar por estado de pago", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status_entry", in="query", description="Filtrar por estado de entrada", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="user_id", in="query", description="Filtrar por ID de usuario", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="event_id", in="query", description="Filtrar por ID de evento", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="person_id", in="query", description="Filtrar por ID de persona", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Entradas", @OA\JsonContent(ref="#/components/schemas/Entry")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(@OA\Property(property="error", type="string")))
     * )
     */


    public function index(IndexEntryRequest $request)
    {

        return $this->getFilteredResults(
            Entry::class,
            $request,
            Entry::filters,
            Entry::sorts,
            EntryResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/entry/{id}",
     *     summary="Obtener detalles de un entry por ID",
     *     tags={"Entry"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID del Entry", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Entrada encontrada", @OA\JsonContent(ref="#/components/schemas/Entry")),
     *     @OA\Response(response=404, description="Entrada No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Entrada No Encontrado")))
     * )
     */

    public function show($id)
    {

        $entry = $this->entryService->getEntryById($id);

        if (!$entry) {
            return response()->json([
                'error' => 'Entrada No Encontrado',
            ], 404);
        }

        return new EntryResource($entry);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/entry",
     *     summary="Crear Entry",
     *     tags={"Entry"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", description="Nombre del entorno"),
     *                 @OA\Property(property="entry_datetime", type="string", format="date", description="Fecha de la entrada (opcional)"),
     *                 @OA\Property(property="code_pay", type="string", description="Código de pago (opcional)"),
     *                 @OA\Property(property="quantity", type="string", description="Cantidad (opcional)"),
     *                 @OA\Property(property="status_pay", type="string", description="Estado del pago", example="Pendiente"),
     *                 @OA\Property(property="status_entry", type="string", description="Estado de la entrada", example="Activo"),
     *                 @OA\Property(property="event_id", type="string", description="ID del evento (opcional)"),
     *                 @OA\Property(property="person_id", type="string", description="ID de la persona (opcional)"),
     *                 @OA\Property(property="company_id", type="integer", description="ID de la empresa", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Entry creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Entry")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al crear el entry")))
     * )
     */

    public function store(StoreEntryRequest $request)
    {

        // 1. Procesar el pago con Culqi
        $result = $this->culquiService->createCharge($request);
        AuditLogService::log('culqi_create_charge', $request->all(), $result);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'El pago falló.',
                'error' => $result['message'] ?? 'Error desconocido en el pago.',
            ], 400);
        }

        return new EntryResource(
            $this->entryService->createEntry([
                ...$request->validated(),
                'reason' => 'compra entrada',
                'user_owner_id' => auth()->id(),
            ])
        );

    }

    /**
     * @OA\Put(
     *     path="/Gia-Backend/public/api/entry/{id}",
     *     summary="Actualizar Entry",
     *     tags={"Entry"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del entry a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", description="Nombre del entorno"),
     *                 @OA\Property(property="entry_datetime", type="string", format="date", description="Fecha de la entrada (opcional)"),
     *                 @OA\Property(property="code_pay", type="string", description="Código de pago (opcional)"),
     *                 @OA\Property(property="quantity", type="string", description="Cantidad (opcional)"),
     *                 @OA\Property(property="status_pay", type="string", description="Estado del pago", example="Pendiente"),
     *                 @OA\Property(property="status_entry", type="string", description="Estado de la entrada", example="Activo"),
     *                 @OA\Property(property="event_id", type="string", description="ID del evento (opcional)"),
     *                 @OA\Property(property="person_id", type="string", description="ID de la persona (opcional)"),
     *                 @OA\Property(property="company_id", type="integer", description="ID de la empresa", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Entry actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Entry")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al actualizar el entry")))
     * )
     */

    public function update(UpdateEntryRequest $request, $id)
    {

        $validatedData = $request->validated();

        $entry = $this->entryService->getEntryById($id);
        if (!$entry) {
            return response()->json([
                'error' => 'Entrada No Encontrado',
            ], 404);
        }

        $updatedEntry = $this->entryService->updateEntry($entry, $validatedData);
        return new EntryResource($updatedEntry);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/entry/{id}",
     *     summary="Eliminar entry por ID",
     *     tags={"Entry"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID del evento que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Entry eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Entry eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Entry No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Entry No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->entryService->getEntryById($id);

        if (!$deleted) {
            return response()->json([
                'error' => 'Entrada No Encontrado.',
            ], 404);
        }
        $deleted = $this->entryService->destroyById($id);
        return response()->json([
            'message' => 'Entrada eliminado exitosamente',
        ], 200);
    }
}
