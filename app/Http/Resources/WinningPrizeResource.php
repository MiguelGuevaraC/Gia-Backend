<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WinningPrizeResource extends JsonResource
{
    public function toArray($request)
    {
        $ticket = $this?->lottery_ticket;
        $user = $ticket?->userOwner;
        $person = $user?->person;

        return [
            'prize_name' => $this?->name,
            'code_correlative' => $ticket?->code_correlative,
            'winner_name' => $person
                ? trim(implode(' ', array_filter([
                    $person?->names,
                    $person?->father_surname,
                    $person?->mother_surname,
                    $person?->business_name,
                ])))
                : null,
        ];
    }
}
