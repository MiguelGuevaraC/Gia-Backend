<?php
namespace App\Http\Requests\PromotionRequest;

use App\Http\Requests\UpdateRequest;
use Illuminate\Validation\Rule;

class UpdatePromotionRequest extends UpdateRequest
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
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'precio'      => 'nullable|numeric|min:0',
            'date_start'  => 'required|date_format:Y-m-d H:i:s',
            'date_end'    => 'required|date_format:Y-m-d H:i:s|after_or_equal:date_start',
            'stock'       => 'nullable|integer|min:0',
            'status'      => 'nullable|string|in:Activo,Inactivo',
            'product_id'  => 'nullable|exists:promotions,id', // Validación del ID del producto
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => 'El nombre es obligatorio.',
            'name.string'          => 'El nombre debe ser una cadena de texto.',
            'name.max'             => 'El nombre no debe exceder los 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string'   => 'La descripción debe ser una cadena de texto.',
            'description.max'      => 'La descripción no debe exceder los 1000 caracteres.',

            'precio.required'      => 'El precio es obligatorio.',
            'precio.numeric'       => 'El precio debe ser un número.',
            'precio.min'           => 'El precio debe ser mayor o igual a 0.',

            'date_start.required'    => 'La fecha de inicio es obligatoria.',
            'date_start.date_format' => 'La fecha de inicio debe tener el formato Y-m-d H:i:s.',
            'date_end.required'      => 'La fecha de fin es obligatoria.',
            'date_end.date_format'   => 'La fecha de fin debe tener el formato Y-m-d H:i:s.',
            'date_end.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'stock.required'       => 'El stock es obligatorio.',
            'stock.integer'        => 'El stock debe ser un número entero.',
            'stock.min'            => 'El stock debe ser como mínimo 0.',

            'status.string'        => 'El estado debe ser una cadena de texto.',
            'status.in'            => 'El estado debe ser "Activo" o "Inactivo".',

            'product_id.required'  => 'El ID del producto es obligatorio.',
            'product_id.exists'    => 'El ID del producto no existe en la base de datos.',
        ];
    }


}
