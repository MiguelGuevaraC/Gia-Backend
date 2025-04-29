<?php
namespace App\Http\Requests\PermissionRequest;

use App\Http\Requests\UpdateRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends UpdateRequest
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
            'name'            => 'required|string|max:255',
            'type'            => 'nullable|string',
            'status'          => 'nullable|string',
            'group_option_id' => 'required|exists:group_options,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'            => 'El nombre es obligatorio.',
            'name.string'              => 'El nombre debe ser una cadena de texto.',
            'name.max'                 => 'El nombre no debe exceder los 255 caracteres.',

            'type.required'            => 'El tipo es obligatorio.',
            'type.string'              => 'El tipo debe ser una cadena de texto.',

            'status.numeric'           => 'El estado debe ser un número.',
            'status.min'               => 'El estado no puede ser negativo.',

            'group_option_id.required' => 'El grupo de opción es obligatorio.',
            'group_option_id.exists'   => 'El grupo de opción seleccionado no es válido.',
        ];
    }


}
