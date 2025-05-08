<?php
namespace App\Http\Requests\GalleryRequest;

use App\Http\Requests\UpdateRequest;
use App\Models\Promotion;
use Illuminate\Validation\Rule;

class UpdateGalleryRequest extends UpdateRequest
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

    public function rules()
    {
        return [
            'images'        => 'required|array|min:1',
    
            // Permitir que cada elemento de images sea un archivo o un array con file y name
            'images.*.file' => [
                'required_without:images.*.name', // Si no hay nombre, el archivo es obligatorio
                'file',
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx',
                'max:4096',
            ],
            'images.*.name' => 'nullable|string|max:255',
        ];
    }


    public function messages()
    {
        return [
            'images.required'                => 'Debe subir al menos un archivo.',
            'images.array'                   => 'El campo imágenes debe ser un arreglo.',
            'images.min'                     => 'Debe subir al menos un archivo.',

            'images.*.file'                  => 'El archivo subido no es válido.',
            'images.*.mimes'                 => 'El archivo debe ser de tipo: jpeg, jpg, png, gif, pdf, doc, docx, xls o xlsx.',
            'images.*.max'                   => 'El archivo no debe superar los 4MB.',

            'images.*.name.string'           => 'El nombre del archivo debe ser una cadena de texto.',
            'images.*.name.max'              => 'El nombre del archivo no debe superar los 255 caracteres.',

            'images.*.file.required_without' => 'Debe proporcionar un archivo si no se ha subido ninguna imagen válida.',
            'images.*.file.required_with'    => 'Debe proporcionar un archivo cuando se especifica un nombre.',
        ];
    }

}
