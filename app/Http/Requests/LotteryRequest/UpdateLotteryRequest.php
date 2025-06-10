<?php
namespace App\Http\Requests\LotteryRequest;

use App\Http\Requests\UpdateRequest;
use App\Models\Lottery;
use Illuminate\Validation\Rule;

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

            'lottery_name' => 'nullable|string|max:255',
            'lottery_description' => 'nullable|string',
            'lottery_date' => 'nullable|date',
            'lottery_price' => 'required|min:1|numeric',
            'status' => 'nullable|string|in:Pendiente,Anulado,Finalizado',
            'winner_id' => 'nullable|integer|exists:users,id',
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


            'lottery_price.numeric' => 'El precio de la rifa debe ser un número.',
            'lottery_price.min' => 'El precio de la rifa no puede ser negativo.',
            'lottery_name.string' => 'El nombre del sorteo debe ser una cadena de texto.',
            'lottery_name.max' => 'El nombre del sorteo no puede exceder los 255 caracteres.',
            'lottery_price.required' => 'El precio del sorteo es obligatorio.',
            'lottery_description.string' => 'La descripción del sorteo debe ser una cadena de texto.',
            'lottery_date.date' => 'La fecha del sorteo debe ser una fecha válida.',
            'status.string' => 'El estado debe ser una cadena de texto.',
            'status.in' => 'El estado debe ser uno de los siguientes: Pendiente, Anulado, Finalizado.',
            'winner_id.integer' => 'El ID del ganador debe ser un número entero.',
            'winner_id.exists' => 'El ganador seleccionado no existe.',

            'event_id.integer' => 'El ID del evento debe ser un número entero.',
            'event_id.exists' => 'El evento seleccionado no existe.',
        ];
    }

       public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Verificamos si se está intentando cambiar el estado a 'Anulado'
            if ($this->input('status') === 'Anulado') {
                $lotteryId = $this->route('id'); // Asegúrate que el parámetro de ruta sea 'id'
                $lottery = Lottery::find($lotteryId);
                if ($lottery && $lottery->tickets()->exists()) {
                    $validator->errors()->add('status', 'No se puede anular el sorteo porque ya tiene tickets registrados.');
                }
            }
        });
    }

}
