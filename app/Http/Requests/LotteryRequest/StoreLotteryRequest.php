<?php
namespace App\Http\Requests\LotteryRequest;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="LotteryRequest",
 *     type="object",
 *     required={"lottery_name", "lottery_description"},
 *     @OA\Property(property="lottery_name", type="string", example="Sorteo Especial", description="Nombre del sorteo"),
 *     @OA\Property(property="lottery_description", type="string", example="Descripción del sorteo especial", description="Descripción del sorteo"),
 *     @OA\Property(property="lottery_date", type="string", format="date-time", example="2025-12-31T23:59:59Z", description="Fecha del sorteo"),
 *     @OA\Property(property="event_id", type="integer", example=2, description="ID del evento asociado al sorteo (opcional)"),
 * )
 */
class StoreLotteryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lottery_name'        => 'required|string|max:255',
            'lottery_description' => 'required|string|max:255',
            'lottery_date'        => 'nullable|date',
            'event_id'            => 'nullable|integer|exists:events,id',
        ];

    }

    public function messages()
    {
        return [

            'lottery_name.required'        => 'El nombre del sorteo es obligatorio.',
            'lottery_name.string'          => 'El nombre del sorteo debe ser una cadena de texto.',
            'lottery_name.max'             => 'El nombre del sorteo no puede exceder los 255 caracteres.',
            'lottery_description.required' => 'La descripción del sorteo es obligatoria.',
            'lottery_description.string'   => 'La descripción del sorteo debe ser una cadena de texto.',
            'lottery_description.max'      => 'La descripción del sorteo no puede exceder los 255 caracteres.',
            'lottery_date.date'            => 'La fecha del sorteo debe ser una fecha válida.',
            'event_id.integer'             => 'El ID del evento debe ser un número entero.',
            'event_id.exists'              => 'El evento seleccionado no existe.',
        ];
    }
}
