<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="GroupOption",
 *     title="GroupOption",
 *     description="Modelo que representa un grupo de opciones",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Usuarios", description="Nombre del grupo de opciones"),
 *     @OA\Property(property="link", type="string", example="/usuarios", description="Ruta o enlace asociado al grupo"),
 *     @OA\Property(property="icon", type="string", example="fas fa-users", description="Ícono para el grupo de opciones"),
 *     @OA\Property(property="status", type="string", example="activo", description="Estado del grupo"),
 * )
 */

 
class GroupOption extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'link',
        'icon',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $hidden = [

        'created_at',
        'updated_at',
        'deleted_at',
    ];
    const filters = [
        'name'   => 'like',
        'status' => 'like',
        'link'   => 'like',
        'icon'   => 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'   => 'desc',
        'type' => 'desc',
        'name' => 'desc',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
