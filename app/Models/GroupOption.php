<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
