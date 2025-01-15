<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'type_document',
        'type_person',
        'number_document',
        'names',
        'father_surname',
        'mother_surname',
        'business_name',

        'address',
        'phone',
        'email',

        'occupation',
        'status',
        'server_id',

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
        'names' => 'like',
        'type_person' => 'like',
        'number_document' => 'like',
        'father_surname' => 'like',
        'mother_surname' => 'like',
        'business_name' => 'like',
        'address' => 'like',
        'phone' => 'like',
        'email' => 'like',
        'occupation' => 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
        'number_document' => 'desc',
        'names' => 'desc',

    ];
}
