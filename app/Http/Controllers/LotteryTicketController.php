<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexLotteryRequest;
use App\Http\Requests\LotteryRequest\IndexLotteryRequest as LotteryRequestIndexLotteryRequest;
use App\Http\Requests\LotteryTicketRequest\IndexLotteryTicketRequest;
use App\Http\Requests\LotteryTicketRequest\StoreAdminLotteryTicketRequest;
use App\Http\Requests\LotteryTicketRequest\StoreLotteryTicketRequest;
use App\Http\Requests\LotteryTicketRequest\UpdateLotteryTicketRequest;
use App\Http\Requests\StoreLotteryRequest;

use App\Http\Resources\LotteryTicketResource;
use App\Models\LotteryTicket;
use App\Services\AuditLogService;
use App\Services\CulquiService;
use App\Services\LotteryTicketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LotteryTicketController extends Controller
{
    protected $lotteryTicketService;
    protected $culquiService;
    public function __construct(LotteryTicketService $lotteryTicketService, CulquiService $culquiService)
    {
        $this->lotteryTicketService = $lotteryTicketService;
        $this->culquiService = $culquiService;
    }


    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/lottery_ticket",
     *     summary="Listar Tickets con filtros",
     *     tags={"Lottery"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="direction", in="query", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Response(response=200, description="Lista de Tickets", @OA\JsonContent(ref="#/components/schemas/Lottery_ticket"))
     * )
     */
    public function index(IndexLotteryTicketRequest $request)
    {
        return $this->getFilteredResults(
            LotteryTicket::class,
            $request,
            LotteryTicket::filters,
            LotteryTicket::sorts,
            LotteryTicketResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/lottery_ticket/{id}",
     *     summary="Mostrar Ticket por ID",
     *     tags={"Lottery"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Ticket encontrado", @OA\JsonContent(ref="#/components/schemas/Lottery_ticket")),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($id)
    {
        try {
            $ticket = $this->lotteryTicketService->getById($id);
            return new LotteryTicketResource($ticket);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/lottery_ticket",
     *     summary="Crear Ticket",
     *     tags={"Lottery"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Lottery_ticketRequest")),
     *     @OA\Response(response=200, description="Ticket creado", @OA\JsonContent(ref="#/components/schemas/Lottery_ticket")),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(StoreLotteryTicketRequest $request)
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

        return new LotteryTicketResource(
            $this->lotteryTicketService->create([
                ...$request->validated(),
                'reason' => 'compra',
                'user_owner_id' => auth()->id(),
            ])
        );

    }

    public function store_admin(StoreAdminLotteryTicketRequest $request)
    {
        return new LotteryTicketResource(
            $this->lotteryTicketService->create([
                ...$request->validated(),
                'reason' => 'admin'
            ])
        );
    }


    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/lottery_ticket/{id}",
     *     summary="Actualizar Ticket",
     *     tags={"Lottery"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Lottery_ticketRequest")),
     *     @OA\Response(response=200, description="Ticket actualizado", @OA\JsonContent(ref="#/components/schemas/Lottery_ticket")),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(UpdateLotteryTicketRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $lotteryticket = $this->lotteryTicketService->getById($id);
            if (!$lotteryticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket de sorteo no encontrado.',
                ], 404);
            }
            $updatedLottery = $this->lotteryTicketService->update($lotteryticket, $validatedData);
            return new LotteryTicketResource(LotteryTicket::find($updatedLottery->id));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el ticket.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/lottery_ticket/{id}",
     *     summary="Eliminar Ticket",
     *     tags={"Lottery"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Eliminado", @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy($id)
    {
        try {
            $this->lotteryTicketService->deleteById($id);
            return response()->json(['message' => 'Ticket eliminado exitosamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
