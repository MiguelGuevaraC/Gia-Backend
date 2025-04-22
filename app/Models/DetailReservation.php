<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailReservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'cant',
        'name',
        'type',
        'precio',
        'precio_total',
        'status',
        'promotion_id',
        'reservation_id',

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
        'ruc'          => 'like',
        'cant'         => 'like',
        'name'         => 'like',
        'type'         => 'like',
        'precio'       => '=',
        'precio_total' => '=',
        'status'       => '=',
        'promotion_id' => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'            => 'desc',
        'business_name' => 'desc',
        'names'         => 'desc',

    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
}
