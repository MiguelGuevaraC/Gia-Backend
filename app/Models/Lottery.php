<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lottery extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'code_serie',
        'lottery_name',
        'lottery_description',
        'lottery_date',
        'status',
        'winner_id',
        'user_created_id',
        'event_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * Filtros disponibles para la búsqueda.
     */
    const filters = [

        'code_serie'          => 'like',
        'event_id'            => '=',
        'lottery_name'        => 'like',
        'lottery_description' => 'like',
        'lottery_date'        => 'date',
        'winner_id'           => '=',
        'user_created_id'     => '=',
        'created_at'          => 'between',
        'status'              => 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
    ];

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
    public function user_created()
    {
        return $this->belongsTo(User::class, 'user_created_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
