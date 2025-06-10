<?php
namespace App\Http\Requests\LotteryRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="LotteryRequest",
 *     type="object",
 *     required={"lottery_name", "lottery_description", "lottery_price", "lottery_date"},
 *     @OA\Property(property="lottery_name", type="string", example="Sorteo Especial", description="Nombre del sorteo"),
 *     @OA\Property(property="lottery_description", type="string", example="Descripción del sorteo especial", description="Descripción del sorteo"),
 *     @OA\Property(property="lottery_price", type="number", format="float", example=10.5, description="Precio del sorteo"),
 *     @OA\Property(property="lottery_date", type="string", format="date-time", example="2025-12-31T23:59:59Z", description="Fecha del sorteo"),
 *     @OA\Property(property="event_id", type="integer", example=2, description="ID del evento asociado al sorteo (opcional)")
 * )
 */
class StoreLotteryRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lottery_name' => 'required|string|max:255',
            'lottery_description' => 'required|string|max:255',
            'lottery_date' => 'required|date',
            'lottery_price' => 'required|min:1|numeric',
            'event_id' => 'nullable|integer|exists:events,id',
            'price_factor_consumo' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(function () {
                    return !is_null($this->input('event_id'));
                }),
            ],
        ];

    }

    public function messages()
    {
        return [

            'price_factor_consumo.required' => 'El factor de consumo es obligatorio cuando se selecciona un evento.',
            'price_factor_consumo.numeric' => 'El factor de consumo debe ser un valor numérico.',
            'price_factor_consumo.min' => 'El factor de consumo debe ser como mínimo 0.',
            'lottery_price.required' => 'El precio del sorteo es obligatorio.',
            'lottery_price.numeric' => 'El precio del sorteo debe ser un número.',
            'lottery_price.min' => 'El precio del sorteo no puede ser negativo ni con valor 0.',
            'lottery_name.required' => 'El nombre del sorteo es obligatorio.',
            'lottery_name.string' => 'El nombre del sorteo debe ser una cadena de texto.',
            'lottery_name.max' => 'El nombre del sorteo no puede exceder los 255 caracteres.',
            'lottery_description.required' => 'La descripción del sorteo es obligatoria.',
            'lottery_description.string' => 'La descripción del sorteo debe ser una cadena de texto.',
            'lottery_description.max' => 'La descripción del sorteo no puede exceder los 255 caracteres.',
            'lottery_date.date' => 'La fecha del sorteo debe ser una fecha válida.',
            'lottery_date.required' => 'La fecha del sorteo es obligatorio.',
            'event_id.integer' => 'El ID del evento debe ser un número entero.',
            'event_id.exists' => 'El evento seleccionado no existe.',
        ];
    }
}
