<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryTicket extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'code_correlative',
        'reason',
        'status',
        'user_owner_id',
        'lottery_id',
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
        'code_correlative' => 'like',
        'reason' => 'like',
        'status' => 'like',
        'user_owner_id' => '=',
        'lottery_id' => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
    ];

    // Relación con el usuario dueño (user_owner_id)
    public function userOwner()
    {
        return $this->belongsTo(User::class, 'user_owner_id');
    }
    public function lottery()
    {
        return $this->belongsTo(Lottery::class, 'lottery_id');
    }
     public function codes()
    {
        return $this->hasOne(CodeAsset::class,'lottery_ticket_id');
    }
}
