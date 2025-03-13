<?php
namespace App\Http\Requests\UserRequest;

use App\Http\Requests\StoreRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class SendTokenAppRequest extends StoreRequest
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
            'email'      => 'required|email|unique:users,username,NULL,id,deleted_at,NULL',
            'phone'      => 'required|string',
            'names'      => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'El correo electrónico debe tener un formato válido.',
            'email.unique'        => 'Este correo electrónico ya está registrado.',
            'phone.required'      => 'El número de teléfono es obligatorio.',
            'phone.string'        => 'El número de teléfono debe ser un texto válido.',
            'names.required'      => 'El nombre es obligatorio.',
            'names.string'        => 'El nombre debe ser un texto válido.',
        ];
    }

}
