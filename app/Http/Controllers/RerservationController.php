<?php
namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest\IndexReservationRequest;
use App\Http\Requests\ReservationRequest\PayReservationRequest;
use App\Http\Requests\ReservationRequest\StoreReservationRequest;
use App\Http\Requests\ReservationRequest\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Station;
use App\Services\AuditLogService;
use App\Services\CodeGeneratorService;
use App\Services\CulquiService;
use App\Services\LotteryService;
use App\Services\LotteryTicketService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RerservationController extends Controller
{
    protected $reservaService;
    protected $culquiService;
    protected $lotteryTicketService;

    protected $codeGeneratorService;

    public function __construct(
        ReservationService $reservaService,
        CulquiService $culquiService,
        LotteryTicketService $lotteryTicketService,
        CodeGeneratorService $codeGeneratorService
    ) {
        $this->reservaService = $reservaService;
        $this->culquiService = $culquiService;
        $this->lotteryTicketService = $lotteryTicketService;
        $this->codeGeneratorService = $codeGeneratorService;
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
        // Obtener el ID del evento y la fecha seleccionada (hoy si no se especifica)
        $event_id = $request->get('event_id');
        $reservationDatetime = $request->get('reservation_datetime', today()->toDateString());

        // Obtener los resultados filtrados
        $results = $this->getFilteredResults(
            Reservation::class,
            $request,
            Reservation::filters,
            Reservation::sorts,
            ReservationResource::class
        );

        // Filtrar las reservas por la fecha seleccionada
        $reservations = collect($results->items())->filter(function ($reservation) use ($reservationDatetime, $event_id) {
            $isToday = Carbon::parse($reservation->reservation_datetime)->isSameDay(Carbon::parse($reservationDatetime));
            $matchesEvent = $event_id ? $reservation->event->id == $event_id : true;
            // return $isToday && $matchesEvent;
            return $matchesEvent;
        });

        // Contar reservas de tipo MESA y BOX
        $reservasMesa = $reservations->where("status", "!=", "Caducado")->where('station.type', 'MESA')->count();
        $reservasBox = $reservations->where("status", "!=", "Caducado")->where('station.type', 'BOX')->count();

        // Contar mesas libres para hoy, filtrando por event_id si es necesario
        $event = Event::find($event_id); // Obtener el evento primero

        $mesasLibres = Station::whereHas('environment', function ($query) use ($event) {
            if ($event) {
                $query->whereHas('company', function ($subQuery) use ($event) {
                    $subQuery->where('id', $event->company_id); // Filtrar por la compañía del evento
                });
            }
        })->whereDoesntHave('reservations', function ($query) use ($reservationDatetime) {
            $query->whereDate('reservation_datetime', '=', $reservationDatetime);
        })->count();

        return response()->json([
            'data' => $reservations,
            'totalReservas' => $reservations->count(),
            'reservasMesa' => $reservasMesa,
            'reservasBox' => $reservasBox,
            'mesasLibres' => $mesasLibres,
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

        if (!$reserva) {
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
        $data = $request->validated();

        $event = Event::findOrFail($data['event_id']);
        $station = Station::findOrFail($data['station_id']);

        $precio = match ($station->type) {
            'MESA' => $event->pricetable,
            'BOX' => $event->pricebox,
            default => null,
        };

        if (is_null($precio)) {
            return response()->json([
                'message' => "No se pudo determinar el precio de la reserva para el tipo de estación '{$station->type}'.",
            ], 422);
        }

        $data['precio_reservation'] = $precio;

        $reserva = $this->reservaService->createReservation($data);

        return new ReservationResource($reserva);
    }

    public function pay_reservation($id_reservation, PayReservationRequest $request)
    {
        try {

            $reservation = Reservation::find($id_reservation);
            if (!$reservation) {
                return response()->json([
                    'error' => 'Reservación No encontrada',
                ], 422);
            }
            if ($reservation->status != 'Pendiente Pago') {

                if ($reservation->status == 'Pagado') {
                    return response()->json([
                        'error' => 'La reserva ya fué Pagada.',
                    ], 422);
                }

                return response()->json([
                    'error' => 'La reserva ya finalizó su tiempo para realizar el pago.',
                ], 422);
            }

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

            $this->reservaService->updateReservation($reservation, ["status" => "Pagado"]);

            $this->afterUpdateReservation($reservation, floatval($request->amount) / 100);


            $resultado = $this->codeGeneratorService->generar('barcode', [
                'description' => 'Reserva',
                'reservation_id' => $reservation->id,
                'lottery_ticket_id' => null,
                'entry_id' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente.',
                // 'payment_data' => $result['object'],
                'data' => $reservation,
            ]);

        } catch (\Exception $e) {
            AuditLogService::log('exception_caught', request()->all(), ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function afterUpdateReservation(Reservation $reservation, float $totalPagado)
    {

        try {
            $event = $reservation->event;

            $lottery = $event->lotteries
                ->sortByDesc('pivot.created_at') // o ->sortByDesc('id') si es más confiable
                ->first();

            if (!$lottery) {
                return;
            }

            $factor = floatval($lottery->pivot->price_factor_consumo);

            $ticketsCount = intval(floor($totalPagado / $factor));

            if ($ticketsCount < 1) {
                return;
            }

            collect(range(1, $ticketsCount))->each(function () use ($lottery, $reservation, $factor) {
                $this->lotteryTicketService->create([
                    'lottery_id' => $lottery->id,
                    'reason' => 'regalo_por_consumo',
                ]);
            });

        } catch (\Throwable $e) {
            // Registrar el error con información de contexto
            AuditLogService::log('lottery_ticket_generation_error', [
                'reservation_id' => $reservation->id,
                'total_pagado' => $totalPagado,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
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
        if (!$reserva) {
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
        $deleted = $this->reservaService->getReservationById($id);

        if (!$deleted) {
            return response()->json([
                'error' => 'Reservación No Encontrado.',
            ], 404);
        }
        $deleted = $this->reservaService->destroyById($id);
        return response()->json([
            'message' => 'Reserva eliminado exitosamente',
        ], 200);
    }
}
