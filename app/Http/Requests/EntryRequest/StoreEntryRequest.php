<?php
namespace App\Http\Requests\EntryRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Event;
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
            'description' => ['nullable', 'string', 'min:5', 'max:80'],
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],

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
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'token.required' => 'El token de pago es obligatorio.',
            'quantity.required' => 'La cantidad de tickets es obligatoria.',
            'quantity.integer' => 'La cantidad de tickets debe ser un número entero.',
            'quantity.min' => 'Debes comprar al menos un ticket.',


            'description.min' => 'La descripción debe tener al menos 5 caracteres.',
            'description.max' => 'La descripción no debe exceder los 80 caracteres.',
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
        $event = Event::find($this->event_id);

        if (!$event) {
            return;
        }

        $submittedAmount = $this->amount / 100;
        $expectedAmount = $event->price_entry * $this->quantity;

        if (abs($submittedAmount - $expectedAmount) > 0.001) {
            $validator->errors()->add(
                'amount',
                'El monto enviado (' . number_format($submittedAmount, 2) . ') no coincide con el precio esperado (' . number_format($expectedAmount, 2) . ') para ' . $this->quantity . ' entrada(s).'
            );
        }
    }


}
