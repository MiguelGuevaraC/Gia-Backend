<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Environment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'route',
        'status',
        'server_id',
        'company_id',
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
        'description' => 'like',
        'route' => 'like',
        'status' => 'like',
        'company.business_name' => 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
        'name' => 'desc',
        'description' => 'desc',

    ];

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
}
