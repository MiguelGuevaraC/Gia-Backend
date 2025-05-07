<?php
namespace App\Http\Requests\PermissionRequest;

use App\Http\Requests\StoreRequest;

/**
 * @OA\Schema(
 *     schema="PermissionRequest",
 *     type="object",
 *     required={"name", "type", "group_option_id"},
 *     @OA\Property(property="name", type="string", description="Nombre del permiso", maxLength=255),
 *     @OA\Property(property="type", type="string", description="Tipo de permiso"),
 *     @OA\Property(property="status", type="integer", format="int32", description="Estado del permiso (opcional, numérico, mínimo 0)"),
 *     @OA\Property(property="link", type="string", description="Enlace asociado al permiso (opcional)"),
 *     @OA\Property(property="group_option_id", type="integer", format="int64", description="ID del grupo de opción al que pertenece el permiso")
 * )
 */

class StorePermissionRequest extends StoreRequest
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
            'name'            => 'required|string|max:255',
            'type'            => 'nullable|string|in:Usuarios,Roles', // Actualizado a obligatorio según el esquema
            'status'          => 'nullable|string|in:Activo,Inactivo',            // Corregido a tipo numérico
            'link'            => 'nullable|string',                   // El campo link sigue siendo opcional
            'group_option_id' => 'required|exists:group_options,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'            => 'El nombre es obligatorio.',
            'name.string'              => 'El nombre debe ser una cadena de texto.',
            'name.max'                 => 'El nombre no debe exceder los 255 caracteres.',

            'type.required'            => 'El tipo es obligatorio.',
            'type.string'              => 'El tipo debe ser una cadena de texto.',
            'type.in'                  => 'El tipo debe ser Usuarios o Roles.',

            'status.numeric'           => 'El estado debe ser un número.',
            'status.min'               => 'El estado no puede ser negativo.',
            'status.in'               => 'El estado solo puede ser Activo, Inactivo.',

            'link.string'              => 'El enlace debe ser una cadena de texto.',

            'group_option_id.required' => 'El grupo de opción es obligatorio.',
            'group_option_id.exists'   => 'El grupo de opción seleccionado no es válido.',
        ];
    }
}
