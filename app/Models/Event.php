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
        'route',
        'comment',
        'pricetable',
        'pricebox',
        'company_id',
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
        'event_datetime' => 'date',
        'comment'        => 'like',
        'nro_reservas'   => '=',
        'nro_boxes'      => '=',
        'status'         => 'like',
        'user_id'        => '=',
        'company_id'     => '=',
        'pricetable'     => '=',
        'pricebox'       => '=',
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
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function reservations_actives()
    {
        return $this->hasMany(Reservation::class)->with(['station'])->whereNot('status', 'Caducado');
    }

    public function activeStations()
    {
        return $this->reservations()
            ->where('status', '!=', 'Caducado')
            ->with('station')
            ->get()
            ->filter(fn($r) => $r->station)     // Asegurarse que tenga estación
            ->unique(fn($r) => $r->station->id) // Solo una reserva por estación
            ->map(function ($reservation) {
                return [
                    'nro_recepcion' => $reservation->id,
                    'status_recepcion'        => $reservation->status,
                    'station'       => $reservation->station,
                ];
            })
            ->values();
    }

}
