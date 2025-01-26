<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'correlative',
        'name',
        'event_datetime',
        'comment',

        'status',
        'user_id',
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

        'name'           => 'like',
        'event_datetime' => 'between',
        'comment'        => 'like',
        'nro_reservas'   => '=',
        'nro_boxes'      => '=',
        'status'         => 'like',
        'user_id'        => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'   => 'desc',
        'name' => 'desc',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
