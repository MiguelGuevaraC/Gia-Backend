<?php

namespace App\Http\Requests\LotteryTicketRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Validator;
use App\Models\Lottery;

/**
 * @OA\Schema(
 *     schema="StoreAdminLotteryTicketRequest",
 *     type="object",
 *     required={ "lottery_id"},
 * @OA\Property(property="lottery_id", type="integer", description="ID del sorteo"),
 * )
 */
class StoreAdminLotteryTicketRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lottery_id' => 'required|integer|exists:lotteries,id',
        ];
    }

    public function messages(): array
    {
        return [
            'lottery_id.required' => 'El ID del sorteo es obligatorio.',
            'lottery_id.integer' => 'El ID del sorteo debe ser un número entero.',
            'lottery_id.exists' => 'El sorteo seleccionado no existe.',
        ];
    }

}
