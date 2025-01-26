<?php
namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest\IndexReservationRequest;
use App\Http\Requests\ReservationRequest\StoreReservationRequest;
use App\Http\Requests\ReservationRequest\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Station;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class RerservationController extends Controller
{
    protected $reservaService;

    public function __construct(ReservationService $reservaService)
    {
        $this->reservaService = $reservaService;
    }
/**
 * @OA\Get(
 *     path="/Gia-Backend/public/api/reservation",
 *     summary="Obtener reservas con filtros",
 *     tags={"Reservation"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="correlative", in="query", description="Filtrar por número correlativo", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="reservation_datetime", in="query", description="Filtrar por fecha y hora de la reservación (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="nro_people", in="query", description="Filtrar por número de personas asociadas a la reservación", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="status", in="query", description="Estado de la reservación", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="user_id", in="query", description="Filtrar por ID del usuario que realizó la reservación", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="reserva_id", in="query", description="Filtrar por ID del reservao relacionado", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="station_id", in="query", description="Filtrar por ID de la estación relacionada", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="person_id", in="query", description="Filtrar por ID de la persona asociada a la reservación", required=false, @OA\Schema(type="string", maxLength=255)),
 *     @OA\Parameter(name="from", in="query", description="Fecha de inicio (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Parameter(name="to", in="query", description="Fecha de fin (YYYY-MM-DD)", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Response(response=200, description="Reservas obtenidas", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Reservation"))),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string")))
 * )
 */

    public function index(IndexReservationRequest $request)
    {
        // Obtener los resultados paginados ya filtrados
        $results = $this->getFilteredResults(
            Reservation::class,
            $request,
            Reservation::filters,
            Reservation::sorts,
            ReservationResource::class
        );

                                                    // Obtener las reservas de los resultados
        $reservations = collect($results->items()); // Convertimos el array en una colección

                                                 // Calcular las cantidades de las reservas basadas en los resultados
        $totalReservas = $reservations->count(); // Total de reservas
        $reservasMesa  = $reservations->filter(function ($reservation) {
            return $reservation->type === 'MESA';
        })->count(); // Reservas de tipo MESA
        $reservasBox = $reservations->filter(function ($reservation) {
            return $reservation->type === 'BOX';
        })->count(); // Reservas de tipo BOX

                                                              // Contar mesas libres, considerando que STATION no tiene relación con reservation
        $environmentId = $request->get('enviroment_id' ?? 1); // El ID de tu ambiente (puedes cambiar este valor según lo necesites)

        $mesasLibres = Station::whereDoesntHave('reservations', function($query) use ($environmentId) {
            $query->where('status', 'Reservado');
            $query->whereHas('environment', function($subQuery) use ($environmentId) {
                $subQuery->where('id', $environmentId); // Filtra por environment_id
            });
        })->count(); // Mesas sin reservas activas en el environment dado

        // Devolver la respuesta con las métricas y los resultados
        return response()->json([
            'data'          => $results,
            'totalReservas' => $totalReservas,
            'reservasMesa'  => $reservasMesa,
            'reservasBox'   => $reservasBox,
            'mesasLibres'   => $mesasLibres,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/reservation/{id}",
     *     summary="Obtener detalles de un reserva por ID",
     *     tags={"Reservation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la reserva", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="reserva encontrada", @OA\JsonContent(ref="#/components/schemas/Reservation")),
     *     @OA\Response(response=404, description="Reservación No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Reservación No Encontrado")))
     * )
     */

    public function show($id)
    {

        $reserva = $this->reservaService->getReservationById($id);

        if (! $reserva) {
            return response()->json([
                'error' => 'Reservación No Encontrado',
            ], 404);
        }

        return new ReservationResource($reserva);
    }

/**
 * @OA\Post(
 *     path="/Gia-Backend/public/api/reservation",
 *     summary="Crear una nueva reservación",
 *     tags={"Reservation"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "reservation_datetime"},
 *                 @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la reservación"),
 *                 @OA\Property(property="reservation_datetime", type="string", format="date", description="Fecha y hora de la reservación (YYYY-MM-DD)"),
 *                 @OA\Property(property="nro_people", type="string", maxLength=255, description="Número de personas asociadas a la reservación", nullable=true),
 *                 @OA\Property(property="status", type="string", description="Estado de la reservación", nullable=true),
 *                 @OA\Property(property="event_id", type="string", maxLength=255, description="ID del evento relacionado a la reservación", nullable=true),
 *                 @OA\Property(property="station_id", type="string", maxLength=255, description="ID de la estación asociada", nullable=true),
 *                 @OA\Property(property="person_id", type="string", maxLength=255, description="ID de la persona asociada a la reservación", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Reservación creada exitosamente", @OA\JsonContent(ref="#/components/schemas/Reservation")),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al crear la reservación")))
 * )
 */

    public function store(StoreReservationRequest $request)
    {
        $reserva = $this->reservaService->createReservation($request->validated());
        return new ReservationResource($reserva);
    }

/**
 * @OA\Put(
 *     path="/Gia-Backend/public/api/reservation/{id}",
 *     summary="Editar una reservación existente",
 *     tags={"Reservation"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la reservación a editar",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "reservation_datetime"},
 *                 @OA\Property(property="name", type="string", maxLength=255, description="Nombre de la reservación"),
 *                 @OA\Property(property="reservation_datetime", type="string", format="date", description="Fecha y hora de la reservación (YYYY-MM-DD)"),
 *                 @OA\Property(property="nro_people", type="string", maxLength=255, description="Número de personas asociadas a la reservación", nullable=true),
 *                 @OA\Property(property="status", type="string", description="Estado de la reservación", nullable=true),
 *                 @OA\Property(property="event_id", type="string", maxLength=255, description="ID del evento relacionado a la reservación", nullable=true),
 *                 @OA\Property(property="station_id", type="string", maxLength=255, description="ID de la estación asociada", nullable=true),
 *                 @OA\Property(property="person_id", type="string", maxLength=255, description="ID de la persona asociada a la reservación", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Reservación actualizada exitosamente", @OA\JsonContent(ref="#/components/schemas/Reservation")),
 *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="Error al actualizar la reservación"))),
 *     @OA\Response(response=404, description="Reservación no encontrada", @OA\JsonContent(@OA\Property(property="error", type="string", example="La reservación no existe")))
 * )
 */

    public function update(UpdateReservationRequest $request, $id)
    {

        $validatedData = $request->validated();

        $reserva = $this->reservaService->getReservationById($id);
        if (! $reserva) {
            return response()->json([
                'error' => 'Reservación No Encontrado',
            ], 404);
        }

        $updatedReserva = $this->reservaService->updateReservation($reserva, $validatedData);
        return new ReservationResource($updatedReserva);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/reservation/{id}",
     *     summary="Eliminar reserva por ID",
     *     tags={"Reservation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la reserva que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Reserva eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Reserva eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Reserva No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Reserva No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->reservaService->destroyById($id);

        if (! $deleted) {
            return response()->json([
                'error' => 'Reservación No Encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Reserva eliminado exitosamente',
        ], 200);
    }
}
