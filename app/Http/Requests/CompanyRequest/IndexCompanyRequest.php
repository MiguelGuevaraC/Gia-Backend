<?php

namespace App\Http\Requests\CompanyRequest;

use App\Http\Requests\IndexRequest;

class IndexCompanyRequest extends IndexRequest
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
            'ruc' => 'nullable|string',
            'business_name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'status' => 'nullable|string',
        ];
    }
}
