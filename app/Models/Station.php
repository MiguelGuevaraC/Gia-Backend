<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'type',

        'status',
        'server_id',
        'route',
        'environment_id',
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
        'type' => 'like',
        'status' => 'like',
        'environment.name' => 'like',
        'environment_id' => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
        'name' => 'desc',
        'description' => 'desc',

    ];

    public function environment()
    {
        return $this->belongsTo(Environment::class, 'environment_id');
    }

    public function getReservationDatetime()
    {
        $reservation = $this->belongsTo(Reservation::class, 'station_id')
                            ->where('status', 'Reservado')
                            ->latest('reservation_datetime') // Ordena por el campo de fecha de reserva
                            ->first();
    
        // Retornar el valor de 'reservation_datetime' o un mensaje predeterminado
        return $reservation ? $reservation->reservation_datetime : 'No hay reserva existente para esta mesa.';
    }

  // Modelo Station
// Modelo Station
public function reservationsactive($environmentId)
{
    return $this->reservations()->where('status', 'Reservado')
                                ->whereHas('environment', function($query) use ($environmentId) {
                                    $query->where('id', $environmentId); // Filtra por environment_id
                                });
}


    
    
}
