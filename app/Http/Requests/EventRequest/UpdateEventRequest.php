<?php
namespace App\Http\Requests\EventRequest;

use App\Http\Requests\UpdateRequest;
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
            'event_datetime' => 'required|date',            // Debe ser una fecha válida
            'comment'        => 'nullable|string|max:1000', // Permitir comentarios opcionales
            'status'         => 'nullable|string',          // Asegurar que sea true o false
        ];
    }

    public function messages()
    {
        return [
            'id.integer'              => 'El ID debe ser un número entero.',

            'name.required'           => 'El nombre es obligatorio.',
            'name.string'             => 'El nombre debe ser una cadena de texto.',
            'name.max'                => 'El nombre no puede tener más de 255 caracteres.',

            'event_datetime.required' => 'La fecha y hora del evento son obligatorias.',
            'event_datetime.date'     => 'La fecha y hora deben ser válidas.',

            'comment.string'          => 'El comentario debe ser una cadena de texto.',
            'comment.max'             => 'El comentario no puede tener más de 1000 caracteres.',

            'status.string'           => 'El estado debe ser una cadena.',

        ];
    }

}
