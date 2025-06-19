<?php
namespace App\Http\Requests\LotteryRequest;

use App\Http\Requests\UpdateRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Prize;
use App\Models\Lottery;
use App\Models\LotteryTicket;

class AssignWinnersRequest extends UpdateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignments' => 'required|array|min:1',

            'assignments.*.prize_id' => [
                'required',
                'integer',
                fn($attr, $val, $fail) =>
                \App\Models\Prize::where('id', $val)->whereNull('deleted_at')->exists()
                ?: $fail("El premio con ID {$val} no existe o ha sido eliminado.")
            ],

            'assignments.*.lottery_ticket_id' => [
                'required',
                'integer',
                fn($attr, $val, $fail) =>
                \App\Models\LotteryTicket::where('id', $val)->whereNull('deleted_at')->exists()
                ?: $fail("El ticket con ID {$val} no existe o ha sido eliminado.")
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'assignments.required' => 'Debe enviar al menos un premio a asignar.',
            'assignments.*.prize_id.required' => 'Cada premio debe tener un ID.',
            'assignments.*.lottery_ticket_id.required' => 'Cada asignación debe tener un ticket ganador.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $lotteryId = $this->route('lottery_id');
            $assignments = collect($this->input('assignments', []));

            $prizeIds = $assignments->pluck('prize_id')->filter();
            $ticketIds = $assignments->pluck('lottery_ticket_id')->filter()->unique();

            $lottery = \App\Models\Lottery::find($lotteryId);
            $lotteryName = $lottery?->lottery_name ?? 'el sorteo indicado';

            // 1. Premios que no pertenecen al sorteo
            $invalidPrizes = \App\Models\Prize::whereIn('id', $prizeIds)
                ->where('lottery_id', '!=', $lotteryId)
                ->get();

            if ($invalidPrizes->isNotEmpty()) {
                $names = $invalidPrizes->pluck('name')->implode(', ');
                $validator->errors()->add(
                    'assignments',
                    "Los siguientes premios no pertenecen al sorteo \"{$lotteryName}\": {$names}."
                );
            }

            // 2. Tickets que no pertenecen al sorteo
            $invalidTickets = \App\Models\LotteryTicket::whereIn('id', $ticketIds)
                ->where('lottery_id', '!=', $lotteryId)
                ->get();

            if ($invalidTickets->isNotEmpty()) {
                $codes = $invalidTickets->pluck('code_correlative')->filter()->implode(', ');
                $mensaje = $codes
                    ? "Los siguientes tickets no pertenecen al sorteo \"{$lotteryName}\": {$codes}."
                    : "Uno o más tickets no pertenecen al sorteo \"{$lotteryName}\".";
                $validator->errors()->add('assignments', $mensaje);
            }

            // 3. Tickets ya asignados como ganadores (excluyendo el premio actual si mantiene su ticket)
            $assignments->each(function ($assignment) use ($lotteryId, $lotteryName, $validator) {
                $ticketId = $assignment['lottery_ticket_id'];
                $prizeId = $assignment['prize_id'];

                // Verificamos si ese ticket ya está asignado a otro premio del mismo sorteo (o a sí mismo pero con otro ticket)
                $conflictingPrize = Prize::where('lottery_id', $lotteryId)
                    ->where('lottery_ticket_id', $ticketId)
                    ->where('id', '!=', $prizeId) // excluir el mismo premio
                    ->with('lottery_ticket')
                    ->first();

                if ($conflictingPrize && $conflictingPrize->lottery_ticket) {
                    $validator->errors()->add(
                        'assignments',
                        "El ticket con código {$conflictingPrize->lottery_ticket->code_correlative} ya fue asignado como ganador del premio \"{$conflictingPrize->name}\" en el sorteo \"{$lotteryName}\"."
                    );
                }
            });

            // // 4. Todos los premios ya tienen ganador
            // $total = \App\Models\Prize::where('lottery_id', $lotteryId)->count();
            // $asignados = \App\Models\Prize::where('lottery_id', $lotteryId)->whereNotNull('lottery_ticket_id')->count();

            // if ($total > 0 && $total === $asignados) {
            //     $validator->errors()->add(
            //         'lottery_id',
            //         "El sorteo \"{$lotteryName}\" ya tiene todos sus premios asignados. No se puede editar."
            //     );
            // }
        });
    }




}
