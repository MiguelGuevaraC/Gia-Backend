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
            'user_owner_id' => 'required|integer|exists:users,id',
            'quantity' => ['nullable', 'integer', 'min:1'],

        ];
    }

    public function messages(): array
    {
        return [
            'lottery_id.required' => 'El ID del sorteo es obligatorio.',
            'lottery_id.integer' => 'El ID del sorteo debe ser un número entero.',
            'lottery_id.exists' => 'El sorteo seleccionado no existe.',

            'user_owner_id.required' => 'El ID del usuario es obligatorio.',
            'user_owner_id.integer' => 'El ID del usuario debe ser un número entero.',
            'user_owner_id.exists' => 'El usuario seleccionado no existe.',

            'quantity.required' => 'La cantidad de tickets es obligatoria.',
            'quantity.integer' => 'La cantidad de tickets debe ser un número entero.',
            'quantity.min' => 'Debes comprar al menos un ticket.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateLotteryDate($validator);
        });
    }

    private function validateLotteryDate(Validator $validator): void
    {
        $lottery = Lottery::find($this->lottery_id);

        if (!$lottery) {
            return;
        }

        if (now()->greaterThan($lottery->lottery_date)) {
            $validator->errors()->add(
                'lottery_id',
                'El sorteo ya ha finalizado y no se pueden comprar más tickets.'
            );
        }
    }
}
