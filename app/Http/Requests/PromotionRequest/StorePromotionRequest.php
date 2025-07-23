<?php
namespace App\Http\Requests\PromotionRequest;

use App\Http\Requests\StoreRequest;

/**
 * @OA\Schema(
 *     schema="PromotionRequest",
 *     type="object",
 *     required={"name", "description", "precio", "date_start", "date_end", "stock", "product_id"},
 *     @OA\Property(property="name", type="string", description="Nombre de la promoción", maxLength=255),
 *     @OA\Property(property="description", type="string", description="Descripción de la promoción"),
 *     @OA\Property(property="precio", type="number", format="float", description="Precio de la promoción"),
 *     @OA\Property(property="date_start", type="string", format="date", description="Fecha de inicio"),
 *     @OA\Property(property="date_end", type="string", format="date", description="Fecha de fin"),
 *     @OA\Property(property="stock", type="integer", description="Cantidad en stock"),
 *     @OA\Property(property="status", type="string", description="Estado de la promoción (activo, inactivo)"),
 *     @OA\Property(property="product_id", type="integer", description="ID del producto asociado a la promoción")
 * )
 */
class StorePromotionRequest extends StoreRequest
{
    public function authorize()
    {
        return true; // Cambia esto si necesitas autorización específica
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',


            'stock' => 'required|integer|min:0',
            'status' => 'nullable|string|in:Activo,Inactivo',
            'product_id' => 'required|exists:products,id', // Validación del ID del producto
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no debe exceder los 1000 caracteres.',

            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio debe ser mayor o igual a 0.',

            'date_start.required' => 'La fecha de inicio es obligatoria.',
            'date_start.date_format' => 'La fecha de inicio debe tener el formato Y-m-d H:i:s.',
            'date_end.required' => 'La fecha de fin es obligatoria.',
            'date_end.date_format' => 'La fecha de fin debe tener el formato Y-m-d H:i:s.',
            'date_end.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',


            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock debe ser como mínimo 0.',

            'status.string' => 'El estado debe ser una cadena de texto.',
            'status.in' => 'El estado debe ser "Activo" o "Inactivo".',

            'product_id.required' => 'El ID del producto es obligatorio.',
            'product_id.exists' => 'El ID del producto no existe en la base de datos.',

            'route.image' => 'El archivo debe ser una imagen.',
            'route.mimes' => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max' => 'El archivo no puede ser mayor a 2 MB.',
        ];
    }
}
