<?php
namespace App\Http\Requests\ReservationRequest;

use App\Http\Requests\IndexRequest;

class IndexReservationRequest extends IndexRequest
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

            'name'                 => 'nullable|string|max:255',
            'correlative'          => 'nullable|string|max:255',
            'reservation_datetime' => 'nullable|string|max:255',
            'nro_people'           => 'nullable|string|max:255',
            'status'               => 'nullable|string|max:255',
            'user_id'              => 'nullable|string|max:255',
            'event_id'             => 'nullable|string|max:255',
            'station_id'           => 'nullable|string|max:255',
            'person_id'            => 'nullable|string|max:255',

        ];
    }
}
