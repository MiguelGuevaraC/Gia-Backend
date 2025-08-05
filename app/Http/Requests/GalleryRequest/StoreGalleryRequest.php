<?php

namespace App\Http\Requests\GalleryRequest;

use App\Http\Requests\StoreRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="GalleryRequest",
 *     type="object",
 *     required={"company_id", "images"},
 *     @OA\Property(property="company_id", type="integer", example=1, description="ID de la empresa"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="file", type="string", format="binary", description="Archivo de imagen"),
 *             @OA\Property(property="name", type="string", maxLength=255, description="Nombre del archivo (opcional)")
 *         )
 *     )
 * )
 */
class StoreGalleryRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // dd('request');
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'images' => ['required', 'array', 'min:1'],
            'route_drive' => ['nullable'],
            'images.*.file' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif',
                'max:4096', // en kilobytes = 4MB
            ],
            'images.*.name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'El campo empresa es obligatorio.',
            'company_id.exists' => 'La empresa seleccionada no existe.',

            'images.required' => 'Debe subir al menos un archivo.',
            'images.array' => 'El campo imágenes debe ser un arreglo.',
            'images.min' => 'Debe subir al menos un archivo.',

            'images.*.file.required' => 'Cada imagen debe tener un archivo.',
            'images.*.file.file' => 'Cada imagen debe ser un archivo válido.',
            'images.*.file.mimes' => 'El archivo debe ser de tipo: jpeg, jpg, png o gif.',
            'images.*.file.max' => 'El archivo no debe superar los 4MB.',

            'images.*.name.string' => 'El nombre del archivo debe ser una cadena de texto.',
            'images.*.name.max' => 'El nombre del archivo no debe superar los 255 caracteres.',
        ];
    }
}
