<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name_image',
        'route',
        'company_id',
        'user_created_id',

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

        'name_image'      => 'like',
        'route'           => 'like',
        'company_id'      => '=',
        'user_created_id' => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'   => 'desc',
        'type' => 'desc',
        'name' => 'desc',
    ];

    public function user_created()
    {
        return $this->belongsTo(User::class, 'user_created_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
