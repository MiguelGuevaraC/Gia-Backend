<?php
namespace App\Http\Requests\GalleryRequest;

use App\Http\Requests\IndexRequest;

class IndexGalleryRequest extends IndexRequest
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

            'name_image'      => 'nullable|string',
            'company_id'      => 'nullable|string',
            'user_created_id' => 'nullable|string',

        ];
    }
}
