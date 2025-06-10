<?php
namespace App\Http\Requests\LotteryTicketRequest;

use App\Http\Requests\IndexRequest;

class IndexLotteryTicketRequest extends IndexRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [

            'code_serie' => 'nullable|string',
            'lottery_name' => 'nullable|string',
            'lottery_description' => 'nullable|string',
            'lottery_date' => 'nullable|string',
            'status' => 'nullable|string',
            'winner_id' => 'nullable|string',
            'user_created_id' => 'nullable|string',
            'event_id' => 'nullable|string',
            'created_at' => 'nullable|string',

        ];
    }
}
