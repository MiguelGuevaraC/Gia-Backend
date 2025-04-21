<?php
namespace App\Http\Requests\ProductRequest;

use App\Http\Requests\StoreRequest;

/**
 * @OA\Schema(
 *     schema="ProductRequest",
 *     type="object",
 *     required={"name", "description"},
 *     @OA\Property(property="name", type="string", description="Nombre del producto", maxLength=255),
 *     @OA\Property(property="description", type="string", description="Descripción del producto"),
 *     @OA\Property(property="precio", type="number", format="float", description="Precio del producto"),
 *     @OA\Property(property="route", type="string", format="binary", description="Imagen del producto (jpg, jpeg, png, gif, máx 2MB)")
 * )
 */

class StoreProductRequest extends StoreRequest
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
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'precio'      => 'nullable|numeric|min:0',
            'route'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // 2MB = 2048KB
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

            'precio.numeric'       => 'El precio debe ser un número.',
            'precio.min'           => 'El precio no puede ser negativo.',

            'route.image'          => 'El archivo debe ser una imagen.',
            'route.mimes'          => 'El archivo debe ser de tipo: jpg, jpeg, png, gif.',
            'route.max'            => 'El archivo no puede ser mayor a 2 MB.',
        ];
    }

}
