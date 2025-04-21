<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'precio',
        'status',
        'server_id',
        'route',

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
        'name'=> 'like',
        'description'=> 'like',
        'precio'=> 'like',
        'status'=> 'like',
        'server_id'=> '=',
        'route'=> 'like',

    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'            => 'desc',
    ];

}
