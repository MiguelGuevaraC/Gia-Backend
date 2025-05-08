<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'price',
        'sort',
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
        'name'                   => 'like',
        'type'                   => 'like',
        'status'                 => 'like',
        'environment.name'       => 'like',
        'environment_id'         => '=',
        'environment.company_id' => '=',
        'price'                  => '=',
        'sort'                   => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'          => 'desc',
        'name'        => 'desc',
        'description' => 'desc',

    ];

    public function environment()
    {
        return $this->belongsTo(Environment::class, 'environment_id');
    }

    public static function updateStatus()
    {
        DB::statement("
            UPDATE stations
            LEFT JOIN (
                SELECT DISTINCT station_id
                FROM reservations
                WHERE DATE(reservation_datetime) = CURDATE()
            ) AS r ON stations.id = r.station_id
            SET stations.status = CASE
                WHEN r.station_id IS NOT NULL THEN 'Reservado'
                WHEN r.station_id IS NULL AND stations.status = 'Reservado' THEN 'Disponible'
                ELSE stations.status
            END
        ");
    }

    public function getReservationDatetime()
    {
        $reservation = $this->hasMany(Reservation::class, 'station_id')
            ->whereDate('reservation_datetime', '=', now()->toDateString())
            ->latest('reservation_datetime') // Ordena por el campo de fecha de reserva
            ->first();

        // Retornar el valor de 'reservation_datetime' o un mensaje predeterminado
        return $reservation ? $reservation->reservation_datetime : 'No hay reserva existente para esta mesa.';
    }

    public function getReservation($reservation)
    {
        $reservation = $this->hasMany(Reservation::class, 'station_id')
            ->whereDate('reservation_datetime', now()->toDateString())
            ->latest('reservation_datetime')
            ->first();
    
        return $reservation ? [
            "person"       => $reservation->person,
            "nro_people"   => $reservation->nro_people,
            "event_name"   => optional($reservation->event)->name,
        ] : null;
    }
    

    public function getReservationStatus()
    {
        $reservation = $this->hasMany(Reservation::class, 'station_id')
            ->whereDate('reservation_datetime', today()) // Verifica si la fecha de la reserva es hoy
            ->latest('reservation_datetime')             // Ordena por el campo de fecha de reserva
            ->first();

        // Retornar el valor de 'reservation_datetime' o un mensaje predeterminado
        return $reservation ? "Reservado" : 'Disponible';
    }

    // Modelo Station
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'station_id');
    }

    public function isAvailableForEvent($eventId)
    {
        return $this->reservations()
            ->where('event_id', $eventId)
            ->where('status', '!=', 'Caducado')
            ->exists();
    }

    public function getReservaForEvent($eventId)
    {
        return $this->reservations()
        ->where('event_id', $eventId)
        ->where('status', '!=', 'Caducado')
        ->latest() // ordena por timestamps (por defecto created_at)
        ->first();
        
    }
    

}
