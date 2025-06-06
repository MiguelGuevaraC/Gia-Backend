<?php
namespace App\Http\Controllers;

use App\Http\Requests\LotteryRequest\IndexLotteryRequest;
use App\Http\Requests\LotteryRequest\StoreLotteryRequest;
use App\Http\Requests\LotteryRequest\UpdateLotteryRequest;
use App\Http\Resources\LotteryResource;
use App\Models\Lottery;
use App\Services\LotteryService;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    protected $lotteryService;

    public function __construct(LotteryService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/lottery",
     *     summary="Obtener información de los sorteos con filtros y ordenamiento",
     *     tags={"Lottery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="page", in="query", description="Número de página para paginación", required=false, @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", description="Número de resultados por página", required=false, @OA\Schema(type="integer", default=10)),
     *    @OA\Parameter(name="sort", in="query", description="Campo por el cual ordenar (ejemplo: id, created_at)", required=false, @OA\Schema(type="string")),
     *    @OA\Parameter(name="direction", in="query", description="Dirección de ordenamiento (asc o desc)", required=false, @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")),
     *    @OA\Parameter(name="code_serie", in="query", description="Filtrar por código de serie", required=false, @OA\Schema(type="string")),
     *    @OA\Parameter(name="event_id", in="query", description="Filtrar por ID de evento", required=false, @OA\Schema(type="integer")),
     *    @OA\Parameter(name="lottery_name", in="query", description="Filtrar por nombre de sorteo", required=false, @OA\Schema(type="string")),
     *    @OA\Parameter(name="lottery_description", in="query", description="Filtrar por descripción de sorteo", required=false, @OA\Schema(type="string")),
     *    @OA\Parameter(name="lottery_date", in="query", description="Filtrar por fecha de sorteo", required=false, @OA\Schema(type="string", format="date")),
     *    @OA\Parameter(name="winner_id", in="query", description="Filtrar por ID de ganador", required=false, @OA\Schema(type="integer")),
     *    @OA\Parameter(name="user_created_id", in="query", description="Filtrar por ID de usuario creador", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="created_at", in="query", description="Filtrar por fecha de creación", required=false, @OA\Schema(type="string", format="date-time")),
     *     @OA\Parameter(name="status", in="query", description="Filtrar por estado (activo, inactivo)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Filtrar desde esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Filtrar hasta esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Sorteoes", @OA\JsonContent(ref="#/components/schemas/Lottery")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */
    public function index(IndexLotteryRequest $request)
    {
        return $this->getFilteredResults(
            Lottery::class,
            $request,
            Lottery::filters,
            Lottery::sorts,
            LotteryResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/lottery/{id}",
     *     summary="Obtener detalles de un lottery por ID",
     *     tags={"Lottery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID del lottery", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Lottery encontrada", @OA\JsonContent(ref="#/components/schemas/Lottery")),
     *     @OA\Response(response=404, description="Sorteo No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Sorteo No Encontrado")))
     * )
     */

    public function show($id)
    {

        $lottery = $this->lotteryService->getLotteryById($id);

        if (! $lottery) {
            return response()->json([
                'error' => 'Sorteo No Encontrado',
            ], 404);
        }

        return new LotteryResource($lottery);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/lottery",
     *     summary="Crear lottery",
     *     tags={"Lottery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     * @OA\Schema(ref="#/components/schemas/LotteryRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Lottery creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Lottery")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un lottery asociado.")))
     * )
     */
    public function store(StoreLotteryRequest $request)
    {
        $lotery = $this->lotteryService->createLottery($request->validated());
        return new LotteryResource ($lotery);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/lottery/{id}",
     *     summary="Actualizar lottery",
     *     tags={"Lottery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del lottery que se desea actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     * @OA\Schema(ref="#/components/schemas/LotteryRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Lottery actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Lottery")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un lottery asociado.")))
     * )
     */

    public function update(UpdateLotteryRequest $request, $id)
    {

        $validatedData = $request->validated();

        $lottery = $this->lotteryService->getLotteryById($id);
        if (! $lottery) {
            return response()->json([
                'error' => 'Sorteo No Encontrado',
            ], 404);
        }

        $updatedLottery = $this->lotteryService->updateLottery($lottery, $validatedData);
        return new LotteryResource(Lottery::find($updatedLottery->id));
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/lottery/{id}",
     *     summary="Eliminar lottery por ID",
     *     tags={"Lottery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Lottery eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Lottery eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Lottery No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Lottery No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {

        $deleted = $this->lotteryService->getLotteryById($id);
        if (! $deleted) {
            return response()->json([
                'error' => 'Sorteo No Encontrado.',
            ], 404);
        }
        $deleted = $this->lotteryService->destroyById($id);
        return response()->json([
            'message' => 'Sorteo eliminado exitosamente',
        ], 200);
    }
}
