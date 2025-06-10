<?php
namespace App\Services;

use App\Models\Lottery;

class LotteryService
{

    public function getLotteryById(int $id): ?Lottery
    {
        return Lottery::find($id);
    }

    public function createLottery(array $data): Lottery
{
    $data['user_created_id'] = auth()->id(); // ID del usuario autenticado
    $data['code_serie'] = str_pad((int) Lottery::max('code_serie') + 1, 4, '0', STR_PAD_LEFT);
    $data['status'] = 'Pendiente';

    // Crear sorteo
    $lottery = Lottery::create($data);

    // Si viene event_id, guardar/actualizar la relación en lottery_by_event
    if (!empty($data['event_id'])) {
        $lottery->events()->syncWithoutDetaching([
            $data['event_id'] => [
                'price_factor_consumo' => $data['price_factor_consumo'] ?? null,
            ]
        ]);
    }

    return $lottery;
}


    public function updateLottery(Lottery $Lottery, array $data): Lottery
    {
        $data = array_intersect_key($data, $Lottery->getAttributes());
        $Lottery->update($data);

        return $Lottery;
    }

    public function destroyById($id)
    {
        $Lottery = Lottery::find($id);

        if (! $Lottery) {
            return false;
        }
        return $Lottery->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
