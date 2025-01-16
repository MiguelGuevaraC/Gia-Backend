<?php
namespace App\Http\Controllers;

use App\Http\Requests\EventRequest\IndexEventRequest;
use App\Http\Requests\EventRequest\StoreEventRequest;
use App\Http\Requests\EventRequest\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

/**
 * @OA\Get(
 *     path="/Gia-Backend/public/api/event",
 *     summary="Obtener eventos con filtros",
 *     tags={"Event"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="event_datetime", in="query", description="Fecha y hora del evento (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="comment", in="query", description="Filtrar por comentarios", required=false, @OA\Schema(type="string", maxLength=1000)),
 *     @OA\Parameter(name="nro_reservas", in="query", description="Número de reservas (≥ 0)", required=false, @OA\Schema(type="integer", minimum=0)),
 *     @OA\Parameter(name="nro_boxes", in="query", description="Número de boxes (≥ 0)", required=false, @OA\Schema(type="integer", minimum=0)),
 *     @OA\Parameter(name="status", in="query", description="Estado del evento", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="user_id", in="query", description="ID del usuario", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Response(response=200, description="Eventos obtenidos", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Event"))),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string")))
 * )
 */

    public function index(IndexEventRequest $request)
    {

        return $this->getFilteredResults(
            Event::class,
            $request,
            Event::filters,
            Event::sorts,
            EventResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/event/{id}",
     *     summary="Obtener detalles de un event por ID",
     *     tags={"Event"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID del evento", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="persona encontrada", @OA\JsonContent(ref="#/components/schemas/Event")),
     *     @OA\Response(response=404, description="Evento No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Evento No Encontrado")))
     * )
     */

    public function show($id)
    {

        $event = $this->eventService->getEventById($id);

        if (! $event) {
            return response()->json([
                'error' => 'Evento No Encontrado',
            ], 404);
        }

        return new EventResource($event);
    }

/**
 * @OA\Post(
 *     path="/Gia-Backend/public/api/event",
 *     summary="Crear un evento",
 *     tags={"Event"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "event_datetime"},
 *                 @OA\Property(property="name", type="string", maxLength=255, description="Nombre del evento"),
 *                 @OA\Property(property="event_datetime", type="string", format="date", description="Fecha y hora del evento (YYYY-MM-DD)"),
 *                 @OA\Property(property="comment", type="string", maxLength=1000, description="Comentario sobre el evento", nullable=true),
 *                 @OA\Property(property="status", type="string", description="Estado del evento", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Evento creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Event")),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al crear el evento")))
 * )
 */

    public function store(StoreEventRequest $request)
    {
        $event = $this->eventService->createEvent($request->validated());
        return new EventResource($event);
    }

/**
 * @OA\Put(
 *     path="/Gia-Backend/public/api/event/{id}",
 *     summary="Editar un evento",
 *     tags={"Event"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del evento a editar",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "event_datetime"},
 *                 @OA\Property(property="name", type="string", maxLength=255, description="Nombre del evento"),
 *                 @OA\Property(property="event_datetime", type="string", format="date", description="Fecha y hora del evento (YYYY-MM-DD)"),
 *                 @OA\Property(property="comment", type="string", maxLength=1000, description="Comentario sobre el evento", nullable=true),
 *                 @OA\Property(property="status", type="string", description="Estado del evento", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Evento actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Event")),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al actualizar el evento"))),
 *     @OA\Response(response=404, description="Evento no encontrado", @OA\JsonContent(@OA\Property(property="error", type="string", example="El evento no existe")))
 * )
 */

    public function update(UpdateEventRequest $request, $id)
    {

        $validatedData = $request->validated();

        $event = $this->eventService->getEventById($id);
        if (! $event) {
            return response()->json([
                'error' => 'Evento No Encontrado',
            ], 404);
        }

        $updatedEvent = $this->eventService->updateEvent($event, $validatedData);
        return new EventResource($updatedEvent);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/event/{id}",
     *     summary="Eliminar event por ID",
     *     tags={"Event"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID del evento que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Event eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Event eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Event No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Event No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->eventService->destroyById($id);

        if (! $deleted) {
            return response()->json([
                'error' => 'Evento No Encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Event eliminado exitosamente',
        ], 200);
    }
}
