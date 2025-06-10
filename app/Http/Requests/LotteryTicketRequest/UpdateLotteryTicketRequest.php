<?php

namespace App\Http\Requests\LotteryTicketRequest;

use App\Http\Requests\UpdateRequest;
use App\Models\Lottery;
use App\Models\LotteryTicket;
use Illuminate\Validation\Validator;

class UpdateLotteryTicketRequest extends UpdateRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:Pendiente,Finalizado,Anulado',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'El estado debe ser "Pendiente", "Finalizado" o "Anulado".',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $ticketId = $this->route('id'); // O usa 'ticket' según cómo esté definido tu route model binding

            $lotteryTicket = LotteryTicket::find( $ticketId);
            $lottery=Lottery::find($lotteryTicket->lottery_id);

            if (!$lottery) {
                $validator->errors()->add('ticket', 'El sorteo no existe no puede actualizar el ticket.');
                return;
            }
            

            if ($lottery && $lottery->status !== 'Pendiente') {
                $validator->errors()->add('status', 'No se puede actualizar el ticket porque el sorteo ya no está en estado "Pendiente".');
            }
        });
    }
}
