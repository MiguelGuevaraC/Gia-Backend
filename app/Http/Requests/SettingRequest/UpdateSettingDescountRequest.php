<?php
namespace App\Http\Requests\SettingRequest;

use App\Http\Requests\UpdateRequest;
use Illuminate\Validation\Rule;

class UpdateSettingDescountRequest extends UpdateRequest
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
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'El campo numérico es obligatorio.',
            'amount.numeric'  => 'El campo numérico debe ser un número.',
            'amount.min'      => 'El numérico no puede ser negativo.',
            'amount.max'      => 'El monto no puede ser mayor a 100.',
        ];
    }

}
