<?php
namespace App\Http\Requests\EntryRequest;

use App\Http\Requests\StoreRequest;
use App\Models\Event;
use Illuminate\Validation\Validator;

class StoreEntryRequest extends StoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'event_id' => 'nullable|integer|exists:events,id',
            'amount' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'min:5', 'max:80'],
            'email' => ['required', 'email'],
            'token' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'entry_daily_date' => ['nullable', 'date'],
            'company_id' => 'nullable|string|exists:companies,id,deleted_at,NULL',
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
            'description.min' => 'La descripción debe tener al menos 5 caracteres.',
            'description.max' => 'La descripción no debe exceder los 80 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'token.required' => 'El token de pago es obligatorio.',
            'quantity.required' => 'La cantidad de tickets es obligatoria.',
            'quantity.integer' => 'La cantidad de tickets debe ser un número entero.',
            'quantity.min' => 'Debes comprar al menos un ticket.',
            'company_id.required' => 'El campo empresa es obligatorio.',
            'company_id.string' => 'El campo empresa debe ser una cadena de texto.',
            'company_id.exists' => 'La empresa seleccionada no existe o ha sido eliminada.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            if (empty($data['event_id']) && (empty($data['company_id']) || !isset($data['company_id']))) {
                $validator->errors()->add('company_id', 'El campo company_id es obligatorio cuando no se proporciona un evento.');
            }

            if ($this->filled('event_id')) {

                if (!$this->filled('amount')) {
                    $validator->errors()->add('amount', 'El monto es obligatorio cuando se selecciona un evento.');
                } elseif ($this->amount < 600) {
                    $validator->errors()->add('amount', 'El monto mínimo permitido es de 600.');
                }

                $this->validateAmountTotal($validator);
                $this->validateTokenRequirement($validator);
            } else {
                if (empty($data['entry_daily_date'])) {
                    $validator->errors()->add('entry_daily_date', 'El campo entry_daily_date es obligatorio cuando no se proporciona un evento.');
                }
                $this->validateFechaNoMayor14Dias($validator);
                $this->validateNoCoincideConEventoActivo($validator);
            }
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

    private function validateTokenRequirement(Validator $validator): void
    {

        $event = Event::find($this->event_id);


        if ($event && in_array($event->is_daily_event, [false, 0, "false", '0'], true) && !$this->filled('token')) {
            $validator->errors()->add(
                'token',
                'El token de pago es obligatorio para este evento diario.'
            );
        }
    }


    private function validateNoCoincideConEventoActivo($validator)
    {
        if ($this->filled('entry_daily_date') && empty($this->event_id)) {
            $reservationDateStr = date('Y-m-d', strtotime($this->entry_daily_date));

            $evento = Event::whereDate('event_datetime', $reservationDateStr)
                ->where('is_daily_event', '0')
                ->whereNull('deleted_at')
                ->first();

            if ($evento) {
                $validator->errors()->add(
                    'entry_daily_date',
                    "No se puede comprar entrada para el día {$reservationDateStr} porque ya existe un evento activo llamado '{$evento->name}' programado en esa fecha."
                );
            }
        }
    }

    private function validateFechaNoMayor14Dias($validator)
    {
        if ($this->filled('entry_daily_date') && empty($this->event_id)) {
            $entryDate = strtotime($this->entry_daily_date);
            $today = strtotime(date('Y-m-d'));
            $maxDate = strtotime('+14 days', $today);

            if ($entryDate > $maxDate) {
                $diffDays = ceil(($entryDate - $maxDate) / (60 * 60 * 24));
                $validator->errors()->add(
                    'entry_daily_date',
                    "La fecha de la entrada no puede ser mayor a 14 días desde hoy. Te has pasado por {$diffDays} día(s)."
                );
            }
        }
    }
}
