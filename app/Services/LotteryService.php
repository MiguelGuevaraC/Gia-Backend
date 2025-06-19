<?php
namespace App\Services;

use App\Http\Resources\UserOnlyResource;
use App\Models\Lottery;
use App\Models\LotteryTicket;
use App\Models\Prize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class LotteryService
{

    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function getLotteryById(int $id): ?Lottery
    {
        return Lottery::find($id);
    }

    public function createLottery(array $data): Lottery
    {


        try {

            $data['user_created_id'] = auth()->id();
            $data['code_serie'] = str_pad((int) Lottery::max('code_serie') + 1, 4, '0', STR_PAD_LEFT);
            $data['status'] = 'Pendiente';

            $lottery = Lottery::create($data);

            // Asociar evento (si lo hay)
            if (!empty($data['event_id'])) {
                $lottery->events()->syncWithoutDetaching([
                    $data['event_id'] => ['price_factor_consumo' => $data['price_factor_consumo'] ?? null]
                ]);
            }

            // Guardar portada del sorteo
            if (!empty($data['route'])) {
                $this->commonService->store_photo($data, $lottery, 'lotteries');
            }

            // Crear premios (si existen)
            if (!empty($data['prizes']) && is_array($data['prizes'])) {
                foreach ($data['prizes'] as $prizeData) {
                    $prize = Prize::create([
                        'lottery_id' => $lottery->id,
                        'name' => $prizeData['name'] ?? '-',
                        'description' => $prizeData['description'] ?? '-',
                    ]);

                    if (!empty($prizeData['route'])) {
                        $this->commonService->store_photo($prizeData, $prize, 'prizes');
                    }
                }
            }

            return $lottery;
        } catch (\Exception $e) {
            throw new \RuntimeException("Error al crear el sorteo: " . $e->getMessage(), 0, $e);
        }
    }

    public function uniqueParticipants($lotteryId)
    {
        $users = LotteryTicket::with('userOwner')
            ->where('lottery_id', $lotteryId)
            ->whereNotNull('user_owner_id')
            ->get()
            ->pluck('userOwner')
            ->unique('id')
            ->values();

        return $users->isEmpty()
            ? []
            : UserOnlyResource::collection($users);
    }



    public function assignWinnersToPrizes(int $lotteryId, array $assignments): Collection
    {
        $updatedPrizes = collect();

        try {
            collect($assignments)->each(function ($assignment) use ($lotteryId, $updatedPrizes) {
                $prize = Prize::where('id', $assignment['prize_id'])
                    ->where('lottery_id', $lotteryId)
                    ->first();


                $prize->update([
                    'lottery_ticket_id' => $assignment['lottery_ticket_id'],
                ]);

                $prize->refresh();
              
                $updatedPrizes->push($prize);
            });

            return $updatedPrizes;

        } catch (\Throwable $e) {
            Log::error('Error al asignar ganadores a los premios', [
                'lottery_id' => $lotteryId,
                'assignments' => $assignments,
                'exception' => $e->getMessage(),
            ]);

            throw new \Exception('Ocurrió un error al asignar los ganadores. Por favor, verifica los datos enviados.');
        }
    }


    public function updateLottery(Lottery $lottery, array $data): Lottery
    {
        try {
            // 1. Actualizar campos del sorteo
            $lotteryData = array_intersect_key($data, $lottery->getAttributes());
            $lottery->update($lotteryData);

            // 2. Actualizar imagen del sorteo si hay una nueva imagen
            if (isset($data['route'])) {
                $data['route'] = $this->commonService->update_photo($data, $lottery, 'lotteries');
                $lottery->update(['route' => $data['route']]);
            }

            // 3. Actualizar factor de consumo en evento relacionado
            if (!empty($data['event_id']) && isset($data['price_factor_consumo'])) {
                $lottery->events()->syncWithoutDetaching([
                    $data['event_id'] => ['price_factor_consumo' => $data['price_factor_consumo']]
                ]);
            }

            // 4. Actualizar premios
            if (!empty($data['prizes']) && is_array($data['prizes'])) {
                $incomingIds = collect($data['prizes'])->pluck('id')->filter()->toArray();

                // Eliminar premios que ya no están en el request
                $lottery->prizes()
                    ->whereNotIn('id', $incomingIds)
                    ->delete();

                // Recorrer premios recibidos
                foreach ($data['prizes'] as $prizeData) {
                    if (!empty($prizeData['id'])) {
                        // Buscar premio existente
                        $prize = Prize::where('id', $prizeData['id'])
                            ->where('lottery_id', $lottery->id)
                            ->first();

                        if ($prize) {
                            // Actualizar nombre
                            if (isset($prizeData['name'])) {
                                $prize->name = $prizeData['name'];
                            }
                            if (isset($prizeData['description'])) {
                                $prize->description = $prizeData['description'];
                            }


                            // Actualizar imagen si hay
                            if (isset($prizeData['route'])) {
                                $prizeData['route'] = $this->commonService->update_photo($prizeData, $prize, 'prizes');
                                $prize->route = $prizeData['route'];
                            }

                            $prize->save();
                            continue;
                        }
                    }

                    // Si no hay ID o no coincide con sorteo, se crea uno nuevo
                    $newPrize = new Prize();
                    $newPrize->lottery_id = $lottery->id;
                    $newPrize->name = $prizeData['name'] ?? '-';
                    $newPrize->description = $prizeData['description'] ?? '-';

                    if (isset($prizeData['route'])) {
                        $prizeData['route'] = $this->commonService->update_photo($prizeData, $newPrize, 'prizes');
                        $newPrize->route = $prizeData['route'];
                    }

                    $newPrize->save();
                }
            }

            return $lottery;
        } catch (\Exception $e) {
            throw new \RuntimeException("Error al actualizar el sorteo: " . $e->getMessage(), 0, $e);
        }
    }



    public function destroyById($id)
    {
        $Lottery = Lottery::find($id);

        if (!$Lottery) {
            return false;
        }
        return $Lottery->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
