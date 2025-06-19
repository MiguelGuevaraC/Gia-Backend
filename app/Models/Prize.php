<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prize extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'name_image',
        'route',
        'lottery_id',
        'lottery_ticket_id',
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
        'name' => 'like',
        'route' => 'like',
        'created_at' => 'date',
        'lottery_id' => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
    ];

     public function lottery_ticket()
    {
        return $this->belongsTo(LotteryTicket::class, 'lottery_ticket_id');
    }
}
