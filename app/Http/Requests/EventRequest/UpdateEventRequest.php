<?php
namespace App\Http\Requests\EventRequest;

use App\Http\Requests\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends UpdateRequest
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
            'name'           => 'required|string|max:255',
            'event_datetime' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $eventDate = \Carbon\Carbon::parse($value)->format('Y-m-d');
    
                    $exists = DB::table('events')
                        ->whereDate('event_datetime', $eventDate)
                        ->where('id', '!=', $this->route('event')) // O $this->id si lo tienes directamente
                        ->whereNull('deleted_at')
                        ->exists();
    
                    if ($exists) {
                        $fail("Ya existe un evento programado para el día {$eventDate}.");
                    }
                },
            ],
            'comment'        => 'nullable|string|max:1000',
            'status'         => 'nullable|string',
            'company_id'     => 'required|integer|exists:companies,id,deleted_at,NULL',
            'route'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'pricebox'       => 'required|numeric|min:0',
            'pricetable'     => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [

            'name.required'           => 'El nombre es obligatorio.',
            'name.string'             => 'El nombre debe ser una cadena de texto.',
            'name.max'                => 'El nombre no puede tener más de 255 caracteres.',

            'event_datetime.required' => 'La fecha y hora del evento son obligatorias.',
            'event_datetime.date'     => 'La fecha y hora deben ser válidas.',

            'comment.string'          => 'El comentario debe ser una cadena de texto.',
            'comment.max'             => 'El comentario no puede tener más de 1000 caracteres.',

            'status.string'           => 'El estado debe ser una cadena.',

            'company_id.required'     => 'La compañía es obligatoria.',
            'company_id.integer'      => 'El identificador de la compañía debe ser un número entero.',
            'company_id.exists'       => 'La compañía seleccionada no existe.',

            'route.image'             => 'El archivo debe ser una imagen.',
            'route.mimes'             => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max'               => 'El archivo no puede ser mayor a 2 MB.',

            'pricebox.required' => 'El campo precio de box es obligatorio.',
            'pricebox.numeric' => 'El campo precio de box debe ser un número.',
            'pricebox.min' => 'El precio de box no puede ser menor que 0.',
        
            'pricetable.required' => 'El campo precio de mesa es obligatorio.',
            'pricetable.numeric' => 'El campo precio de mesa debe ser un número.',
            'pricetable.min' => 'El precio de mesa no puede ser menor que 0.',

        ];
    }

}
