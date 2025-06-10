<?php

namespace App\Http\Requests\LotteryTicketRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Validator;
use App\Models\Lottery;

/**
 * @OA\Schema(
 *     schema="StoreLotteryTicketRequest",
 *     type="object",
 *     required={"lottery_id", "amount", "description", "email", "token"},
 *     @OA\Property(property="lottery_id", type="integer", description="ID del sorteo"),
 *     @OA\Property(property="amount", type="number", format="float", description="Monto pagado por el ticket"),
 *     @OA\Property(property="description", type="string", maxLength=255, description="Descripción de la compra"),
 *     @OA\Property(property="email", type="string", format="email", description="Correo del comprador"),
 *     @OA\Property(property="token", type="string", description="Token de pago")
 * )
 */
class StoreLotteryTicketRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lottery_id' => 'required|integer|exists:lotteries,id',
            'amount' => ['required', 'numeric', 'min:600'],
            'description' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'lottery_id.required' => 'El ID del sorteo es obligatorio.',
            'lottery_id.integer' => 'El ID del sorteo debe ser un número entero.',
            'lottery_id.exists' => 'El sorteo seleccionado no existe.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.min' => 'El monto mínimo permitido es de 600.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser un texto.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'token.required' => 'El token de pago es obligatorio.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateAmountTotal($validator);
        });
    }

    private function validateAmountTotal(Validator $validator): void
    {
        $lottery = Lottery::find($this->lottery_id);

        if (!$lottery) {
            return; // Ya validado en "exists"
        }

        $submittedAmount = $this->amount / 100;
        $expectedAmount = $lottery->lottery_price;

        if (abs($submittedAmount - $expectedAmount) > 0.001) {
            $validator->errors()->add(
                'amount',
                'El monto enviado (' . number_format($submittedAmount, 2) . ') no coincide con el precio del sorteo (' . number_format($expectedAmount, 2) . '). ' .
                'Diferencia: ' . number_format(abs($submittedAmount - $expectedAmount), 2)
            );
        }
    }

}
