<?php

namespace App\Http\Requests\PersonRequest;

use App\Http\Requests\IndexRequest;

class IndexPersonRequest extends IndexRequest
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

            'type_document' => 'nullable|string',
            'type_person' => 'nullable|string',
            'number_document' => 'nullable|string',
            'names' => 'nullable|string',
            'father_surname' => 'nullable|string',
            'mother_surname' => 'nullable|string',
            'business_name' => 'nullable|string',

            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',

            'ocupation' => 'nullable|string',
            'status' => 'nullable|string',

        ];
    }
}
