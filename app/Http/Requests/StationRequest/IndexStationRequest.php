<?php

namespace App\Http\Requests\StationRequest;

use App\Http\Requests\IndexRequest;

class IndexStationRequest extends IndexRequest
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

            'name' => 'nullable|string',
            'type' => 'nullable|string',
            'descriptioni' => 'nullable|string',
            'status' => 'nullable|string',

            'environment$name' => 'nullable|string',
            'environment_id' => 'nullable|string',

            'precio' => 'nullable|string',
            'sort' => 'nullable|string',

        ];
    }
}
