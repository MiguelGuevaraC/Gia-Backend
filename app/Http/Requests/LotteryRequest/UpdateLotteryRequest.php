<?php
namespace App\Http\Requests\LotteryRequest;

use App\Http\Requests\UpdateRequest;

class UpdateLotteryRequest extends UpdateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'lottery_name'        => 'nullable|string|max:255',
            'lottery_description' => 'nullable|string',
            'lottery_date'        => 'nullable|date',
            'status'              => 'nullable|string|in:Pendiente,Cancelado,Finalizado',
            'winner_id'           => 'nullable|integer|exists:users,id',
        ];

    }
    public function messages()
    {
        return [
            'lottery_name.string'          => 'El nombre del sorteo debe ser una cadena de texto.',
            'lottery_name.max'             => 'El nombre del sorteo no puede exceder los 255 caracteres.',
            'lottery_description.string'   => 'La descripción del sorteo debe ser una cadena de texto.',
            'lottery_date.date'            => 'La fecha del sorteo debe ser una fecha válida.',
            'status.string'                => 'El estado debe ser una cadena de texto.',
            'status.in'                    => 'El estado debe ser uno de los siguientes: Pendiente, Cancelado, Finalizado.',
            'winner_id.integer'            => 'El ID del ganador debe ser un número entero.',
            'winner_id.exists'             => 'El ganador seleccionado no existe.',
        ];
    }

}
