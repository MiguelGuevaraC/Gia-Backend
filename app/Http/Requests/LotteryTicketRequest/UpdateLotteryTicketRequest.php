<?php

namespace App\Http\Requests\LotteryTicketRequest;

use App\Http\Requests\UpdateRequest;

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
            'reason' => 'nullable|in:compra,regalo_por_consumo',
            'status' => 'nullable|in:Pendiente,Finalizado,Anulado',
            'lottery_id' => 'nullable|integer|exists:lotteries,id',
            'user_owner_id' => 'nullable|integer|exists:users,id',
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'reason.in' => 'La razón debe ser "compra" o "regalo_por_consumo".',
            'status.in' => 'El estado debe ser "Pendiente", "Finalizado" o "Anulado".',
            'lottery_id.integer' => 'El ID del sorteo debe ser un número entero.',
            'lottery_id.exists' => 'El sorteo seleccionado no existe.',
            'user_owner_id.integer' => 'El ID del usuario debe ser un número entero.',
            'user_owner_id.exists' => 'El usuario seleccionado no existe.',
        ];
    }
}
