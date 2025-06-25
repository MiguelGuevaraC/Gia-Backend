<?php

namespace App\Http\Requests\LotteryRequest;

use App\Http\Requests\StoreRequest;
use App\Models\LotteryByEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="LotteryRequest",
 *     type="object",
 *     required={"lottery_name", "lottery_description", "lottery_price", "lottery_date", "prizes"},
 *     @OA\Property(property="lottery_name", type="string", example="Sorteo Especial", description="Nombre del sorteo"),
 *     @OA\Property(property="lottery_description", type="string", example="Descripción del sorteo especial", description="Descripción del sorteo"),
 *     @OA\Property(property="lottery_price", type="number", format="float", example=10.5, description="Precio del sorteo"),
 *     @OA\Property(property="lottery_date", type="string", format="date-time", example="2025-12-31T23:59:59Z", description="Fecha del sorteo"),
 *     @OA\Property(property="event_id", type="integer", example=2, description="ID del evento asociado al sorteo (opcional)"),
 *     @OA\Property(property="price_factor_consumo", type="number", format="float", example=1.2, description="Factor de precio si el sorteo está vinculado a un evento"),
 *     @OA\Property(property="route", type="string", format="binary", description="Imagen general del sorteo"),
 *     @OA\Property(
 *         property="prizes",
 *         type="array",
 *         minItems=1,
 *         @OA\Items(
 *             type="object",
 *             required={"name", "route"},
 *             @OA\Property(property="name", type="string", example="Premio 1", description="Nombre del premio"),
 *             @OA\Property(property="route", type="string", format="binary", description="Imagen del premio")
 *         )
 *     )
 * )
 */
class StoreLotteryRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lottery_name' => 'required|string|max:255',
            'lottery_description' => 'required|string|max:255',
            'lottery_date' => 'required|date',
            'lottery_price' => 'required|min:1|numeric',
            'company_id' => 'required|integer|exists:companies,id,deleted_at,NULL',


            'event_id' => [
                'nullable',
                'integer',
                Rule::exists('events', 'id')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {
                    if (!is_null($value)) {
                        $exists = LotteryByEvent::where('event_id', $value)
                            ->whereNull('deleted_at')
                            ->whereHas('lottery', function ($query) {
                                $query->whereNull('deleted_at');
                            })
                            ->exists();

                        if ($exists) {
                            $fail('Este evento ya tiene un sorteo asignado.');
                        }
                    }
                },
            ],


            'price_factor_consumo' => [
                'nullable',
                'numeric',
                'min:0',
                //Rule::requiredIf(fn() => !is_null($this->input('event_id'))),
            ],

            'route' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',

            'prizes' => 'required|array|min:1',
            'prizes.*.name' => 'required|string|max:255',
            'prizes.*.description' => 'required|string',
            'prizes.*.route' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'lottery_name.required' => 'El nombre del sorteo es obligatorio.',
            'lottery_name.string' => 'El nombre del sorteo debe ser una cadena de texto.',
            'lottery_name.max' => 'El nombre del sorteo no puede exceder los 255 caracteres.',

            'lottery_description.required' => 'La descripción del sorteo es obligatoria.',
            'lottery_description.string' => 'La descripción del sorteo debe ser una cadena de texto.',
            'lottery_description.max' => 'La descripción del sorteo no puede exceder los 255 caracteres.',

            'lottery_date.required' => 'La fecha del sorteo es obligatoria.',
            'lottery_date.date' => 'La fecha del sorteo debe ser una fecha válida.',

            'lottery_price.required' => 'El precio del sorteo es obligatorio.',
            'lottery_price.numeric' => 'El precio del sorteo debe ser un número.',
            'lottery_price.min' => 'El precio del sorteo no puede ser negativo ni con valor 0.',

            'company_id.required' => 'La compañía es obligatoria.',
            'company_id.integer' => 'El identificador de la compañía debe ser un número entero.',
            'company_id.exists' => 'La compañía seleccionada no existe.',


            'event_id.integer' => 'El ID del evento debe ser un número entero.',
            'event_id.exists' => 'El evento seleccionado no existe.',

            'price_factor_consumo.required' => 'El factor de consumo es obligatorio cuando se selecciona un evento.',
            'price_factor_consumo.numeric' => 'El factor de consumo debe ser un valor numérico.',
            'price_factor_consumo.min' => 'El factor de consumo debe ser como mínimo 0.',

            'route.image' => 'El archivo debe ser una imagen.',
            'route.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max' => 'El archivo no puede ser mayor a 2 MB.',

            'prizes.required' => 'Debe ingresar al menos un premio.',
            'prizes.array' => 'Los premios deben enviarse como un arreglo.',
            'prizes.min' => 'Debe registrar al menos un premio.',

            'prizes.*.name.required' => 'El nombre del premio es obligatorio.',
            'prizes.*.name.string' => 'El nombre del premio debe ser una cadena de texto.',
            'prizes.*.name.max' => 'El nombre del premio no puede exceder los 255 caracteres.',

            'prizes.*.description.required' => 'La descripción del premio es obligatorio.',
            'prizes.*.description.string' => 'La descripción del premio debe ser una cadena de texto.',

            'prizes.*.route.required' => 'La imagen del premio es obligatoria.',
            'prizes.*.route.image' => 'Cada imagen del premio debe ser un archivo de imagen.',
            'prizes.*.route.mimes' => 'Cada imagen del premio debe ser de tipo: jpg, jpeg, png, gif.',
            'prizes.*.route.max' => 'Cada imagen del premio no puede superar los 2 MB.',
        ];
    }
}
