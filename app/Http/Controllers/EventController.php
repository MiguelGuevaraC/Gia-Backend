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
     *     summary="Listar eventos con filtros",
     *     tags={"Event"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="event_datetime", in="query", description="Filtrar por fecha (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de eventos", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Event"))),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string")))
     * )
     */
    public function index(IndexEventRequest $request)
    {
        $now = now()->toDateTimeString();

        $query = Event::query()
            ->orderByRaw("
            CASE WHEN event_datetime >= ? THEN 0 ELSE 1 END ASC,
            CASE WHEN event_datetime >= ? THEN event_datetime ELSE NULL END ASC,
            CASE WHEN event_datetime < ? THEN event_datetime ELSE NULL END DESC",
                [$now, $now, $now]
            );

        return $this->getFilteredResults(
            $query,
            $request,
            Event::filters,
            [], // usamos orderByRaw directamente
            EventResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/event/{id}",
     *     summary="Obtener evento por ID",
     *     tags={"Event"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID del evento", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Evento encontrado", @OA\JsonContent(ref="#/components/schemas/Event")),
     *     @OA\Response(response=404, description="No encontrado", @OA\JsonContent(@OA\Property(property="error", type="string")))
     * )
     */

    public function show($id)
    {

        $event = $this->eventService->getEventById($id);

        if (!$event) {
            return response()->json([
                'error' => 'Evento No Encontrado',
            ], 404);
        }

        return new EventResource($event);
    }


    public function events_by_date($date)
    {
        try {
            $eventos = $this->eventService->getEvents_by_date($date);

            return response()->json([
                'event_daily' => $eventos['diario'] ? new EventResource($eventos['diario']) : null,
                'event_particular' => $eventos['particular'] ? new EventResource($eventos['particular']) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
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

     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Evento creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Event")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al crear el evento")))
     * )
     */

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['status'] = 'Reservado';
        $event = $this->eventService->createEvent($data);
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
        if (!$event) {
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
        $event = $this->eventService->getEventById($id);
        if (!$event) {
            return response()->json(['error' => 'Evento no encontrado.'], 404);
        }
        if ($event->reservations()->exists()) {
            return response()->json(['error' => 'El evento tiene reservas asociadas y no se puede eliminar.'], 400);
        }
        $this->eventService->destroyById($id);
        return response()->json(['message' => 'Evento eliminado exitosamente.'], 200);
    }

}
