<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryByEvent extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'price_factor_consumo',
        'event_id',
        'lottery_id',
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
        'price_factor_consumo' => '=',
        'event_id' => '=',
        'lottery_id' => '=',
        'created_at' => 'between',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
    ];

    public function lottery()
    {
        return $this->belongsTo(Lottery::class, 'lottery_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
