<?php
namespace App\Http\Requests\PromotionRequest;

use App\Http\Requests\IndexRequest;

class IndexPromotionRequest extends IndexRequest
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
            'name'        => 'nullable|string',
            'description' => 'nullable|string',
            'precio'      => 'nullable|string',
            'date_start'  => 'nullable|string',
            'date_end'    => 'nullable|string',
            'stock'       => 'nullable|string',
            'status'      => 'nullable|string',
            'from'        => 'nullable',
            'to'          => 'nullable',
        ];
    }
}
