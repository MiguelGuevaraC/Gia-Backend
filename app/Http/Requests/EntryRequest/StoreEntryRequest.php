<?php
namespace App\Http\Requests\EntryRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Validator;

class StoreEntryRequest extends StoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cambia esto si necesitas autorización específica
    }

    public function rules()
    {
        return [
            'event_id' => 'required|integer|exists:events,id',
            'amount' => ['required', 'numeric', 'min:600'],
            'description' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'El ID del evento es obligatorio.',
            'event_id.integer' => 'El ID del evento debe ser un número entero.',
            'event_id.exists' => 'El evento seleccionado no existe.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.min' => 'El monto mínimo permitido es de 600.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser un texto.',
            'description.max' => 'La descripción no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'token.required' => 'El token de pago es obligatorio.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateAmountTotal($validator);
        });
    }

    private function validateAmountTotal(Validator $validator): void
    {
        // $validator->errors()->add(
        //     'amount',
        //     'VALIDAR ANTES DE COMPRAR UNA ENTRADA, PREGUNTAR AL ADMINISTRADOR'
        // );
    }


}
