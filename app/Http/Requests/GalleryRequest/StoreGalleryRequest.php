<?php
namespace App\Http\Requests\GalleryRequest;

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
class StoreGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id'    => 'required|exists:companies,id',
            'images'        => 'required|array|min:1',

            // Validación de cada imagen
            'images.*.file' => [
                'required_without:images.*.name',
                'file',
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx',
                'max:4096',
            ],
            'images.*.name' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required'            => 'El campo empresa es obligatorio.',
            'company_id.exists'              => 'La empresa seleccionada no existe.',

            'images.required'                => 'Debe subir al menos un archivo.',
            'images.array'                   => 'El campo imágenes debe ser un arreglo.',
            'images.min'                     => 'Debe subir al menos un archivo.',

            'images.*.file.required_without' => 'Debe proporcionar un archivo si no se especifica un nombre.',
            'images.*.file'                  => 'El archivo subido no es válido.',
            'images.*.file.mimes'            => 'El archivo debe ser de tipo: jpeg, jpg, png, gif, pdf, doc, docx, xls o xlsx.',
            'images.*.file.max'              => 'El archivo no debe superar los 4MB.',

            'images.*.name.string'           => 'El nombre del archivo debe ser una cadena de texto.',
            'images.*.name.max'              => 'El nombre del archivo no debe superar los 255 caracteres.',
        ];
    }
}
