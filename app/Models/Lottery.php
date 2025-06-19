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
        'lottery_price',
        'route',
        'status',
        'winner_id',
        'user_created_id',
        'event_id',
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
    /**
     * Filtros disponibles para la búsqueda.
     */
    const filters = [

        'route',
        'code_serie' => 'like',
        'event_id' => '=',
        'lottery_name' => 'like',
        'lottery_description' => 'like',
        'lottery_date' => 'date',
        'lottery_price' => '=',
        'winner_id' => '=',
        'user_created_id' => '=',
        'created_at' => 'between',
        'status' => 'like',
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
     public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function events()
    {
        return $this->belongsToMany(Event::class, 'lottery_by_events')
            ->withPivot('price_factor_consumo')
            ->withTimestamps();
    }
    public function lotteryByEvent()
    {
        return $this->hasOne(LotteryByEvent::class);
    }

    

    public function tickets()
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }
}
