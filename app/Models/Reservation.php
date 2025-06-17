<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'correlative',
        'name',
        'reservation_datetime',
        'nro_people',
        'status',
        'expires_at',
        'user_id',
        'event_id',
        'station_id',
        'person_id',
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
        'reservation_datetime' => 'date',
        'nro_people'           => 'like',
        'status'               => 'like',
        'user_id'              => '=',
        'event_id'             => '=',
        'person_id'            => '=',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($movement) {


        });

    }

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'          => 'desc',
        'description' => 'desc',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }
    public function detailReservations()
    {
        return $this->hasMany(DetailReservation::class);
    }
     public function codes()
    {
        return $this->hasOne(CodeAsset::class);
    }
}
