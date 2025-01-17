<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'type',

        'status',
        'server_id',
        'route',
        'environment_id',
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
        'name' => 'like',
        'type' => 'like',
        'status' => 'like',
        'environment.name' => 'like',
        'environment_id' => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
        'name' => 'desc',
        'description' => 'desc',

    ];

    public function environment()
    {
        return $this->belongsTo(Environment::class, 'environment_id');
    }
}
