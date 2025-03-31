<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'ruc',
        'business_name',
        'address',
        'phone',
        'email',
        'route',
        'status',

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
        'ruc'           => 'like',
        'business_name' => 'like',
        'address'       => 'like',
        'phone'         => 'like',
        'email'         => 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'            => 'desc',
        'business_name' => 'desc',
        'names'         => 'desc',

    ];

    public function environments()
    {
        return $this->hasMany(Environment::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function upcomingEvents()
    {
        return $this->hasMany(Event::class)->where('event_datetime', '>=', Carbon::now());
    }

    public function pastEvents()
    {
        return $this->hasMany(Event::class)->where('event_datetime', '<', Carbon::now());
    }

}
