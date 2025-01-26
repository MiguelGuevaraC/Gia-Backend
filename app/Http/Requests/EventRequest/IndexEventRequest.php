<?php
namespace App\Http\Requests\EventRequest;

use App\Http\Requests\IndexRequest;

class IndexEventRequest extends IndexRequest
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
            'event_datetime' => 'nullable|date',            // Debe ser una fecha válida
            'comment'        => 'nullable|string|max:1000', // Permitir comentarios opcionales
            'status'         => 'nullable|string',
            'user_id'        => 'nullable|string',

        ];
    }
}
