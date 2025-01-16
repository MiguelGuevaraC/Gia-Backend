<?php
namespace App\Http\Requests\EntryRequest;

use App\Http\Requests\IndexRequest;

class IndexEntryRequest extends IndexRequest
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

            'name'           => 'nullable|string|max:255',
            'entry_datetime' => 'nullable|date', // Debe ser una fecha válida
            'code_pay'       => 'nullable|string|max:255',
            'quantity'       => 'nullable|string|max:255',
            'status_pay'     => 'nullable|string|max:255',
            'status_entry'   => 'nullable|string|max:255',
            'user_id'        => 'nullable|string|max:255',
            'event_id'       => 'nullable|string|max:255',
            'person_id'      => 'nullable|string|max:255',

        ];
    }
}
