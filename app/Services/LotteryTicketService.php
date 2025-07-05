<?php

namespace App\Services;

use App\Exports\Lottery\LotteryTicketsExport;
use App\Http\Resources\LotteryTicketResource;
use App\Mail\SendTicketMail;
use App\Models\Lottery;
use App\Models\LotteryTicket;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class LotteryTicketService
{
    protected $codeGeneratorService;

    public function __construct(CodeGeneratorService $codeGeneratorService)
    {
        $this->codeGeneratorService = $codeGeneratorService;
    }

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


    public function create(array $data): Collection
    {
        try {
            $lottery = Lottery::findOrFail($data['lottery_id']);

            // Obtener último número correlativo
            $lastNumber = (int) preg_replace(
                '/.*-(\d{8})$/',
                '$1',
                LotteryTicket::where('lottery_id', $lottery->id)
                    ->orderByDesc('code_correlative')
                    ->value('code_correlative') ?? '00000000'
            );
            $quantity = isset($data['quantity']) ? $data['quantity'] : 1;


            return collect(range(1, $quantity))->map(function ($i) use ($data, $lottery, $lastNumber) {
                $codeCorrelative = $lottery->code_serie . '-' . str_pad($lastNumber + $i, 8, '0', STR_PAD_LEFT);

                $ticket = LotteryTicket::create([
                    ...$data,
                    'code_correlative' => $codeCorrelative,
                    'status' => 'Pendiente',
                ]);


                $email = $ticket->userOwner?->person?->email;
                $isValidEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
                $sentStatus = $isValidEmail ? 'ENVIADO' : 'NO_ENVIADO';


                $this->codeGeneratorService->generar('qrcode', [
                    'description' => 'Ticket Sorteo',
                    'reservation_id' => null,
                    'lottery_ticket_id' => $ticket->id,
                    'entry_id' => null,
                ]);

                // Enviar solo si es válido
                if ($isValidEmail) {
                    $this->sendTokenByEmail($email, (new LotteryTicketResource($ticket))->toArray(request()));
                }

                // Registrar intento
                Storage::append('logs/ticket_email.log', sprintf(
                    "[%s] [%s] Ticket ID: %d | userOwner ID: %s | person ID: %s | Email: %s",
                    now(),
                    $sentStatus,
                    $ticket->id,
                    $ticket->userOwner->id ?? 'null',
                    $ticket->userOwner?->person->id ?? 'null',
                    $email ?? 'null'
                ));


                return LotteryTicket::find($ticket->id);
            });

        } catch (\Throwable $e) {
            $this->handleException('Error al crear el/los ticket(s)', $e);
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

    public function getLotteryHistoryForUser(int $userId)
    {
        return LotteryTicket::
            where('user_owner_id', $userId)
            ->orderByDesc('id')
            ->limit(50)
            ->get();
    }


    public function sendTokenByEmail($email, $ticket)
    {
        Mail::to($email)->send(new SendTicketMail($ticket));
    }

}
