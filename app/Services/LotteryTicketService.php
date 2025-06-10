<?php

namespace App\Services;

use App\Models\Lottery;
use App\Models\LotteryTicket;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class LotteryTicketService
{
    protected function handleException(string $message, \Throwable $e = null, int $code = 400): never
    {
        Log::error($message, ['exception' => $e]);
        throw new HttpResponseException(response()->json([
            'message' => $message,
            'error' => $e?->getMessage(),
        ], $code));
    }

    public function getById(int $id): LotteryTicket
    {
        try {
            return LotteryTicket::findOrFail($id);
        } catch (\Throwable $e) {
            $this->handleException('Ticket no encontrado o error al obtenerlo', $e, 404);
        }
    }

    public function create(array $data): LotteryTicket
    {
        try {
            $lottery = Lottery::findOrFail($data['lottery_id']);
            $lastNumber = (int) preg_replace(
                '/.*-(\d{8})$/',
                '$1',
                LotteryTicket::where('lottery_id', $lottery->id)
                    ->orderByDesc('code_correlative')
                    ->value('code_correlative') ?? '00000000'
            );

            $codeCorrelative = $lottery->code_serie . '-' . str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT);

            return LotteryTicket::create([
                ...$data,
                'code_correlative' => $codeCorrelative,
                'user_owner_id' => auth()->id(),
                'status' => 'Pendiente',
            ]);
        } catch (\Throwable $e) {
            $this->handleException('Error al crear el ticket', $e);
        }
    }



    public function update(LotteryTicket $ticket, array $data): LotteryTicket
    {
        try {
            $ticket->update(array_intersect_key($data, $ticket->getAttributes()));
            return $ticket;
        } catch (\Throwable $e) {
            $this->handleException('Error al actualizar el ticket', $e);
        }
    }

    public function deleteById(int $id): bool
    {
        try {
            return $this->getById($id)->delete();
        } catch (\Throwable $e) {
            $this->handleException('Error al eliminar el ticket', $e);
        }
    }
}
