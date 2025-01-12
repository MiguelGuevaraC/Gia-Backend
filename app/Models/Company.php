<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'ruc',
        'business_name',
        'address',
        'phone',
        'email',
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
        'ruc'=> 'like',
        'business_name'=> 'like',
        'address'=> 'like',
        'phone'=> 'like',
        'email'=> 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
        'business_name' => 'desc',
        'names' => 'desc',

    ];
}
