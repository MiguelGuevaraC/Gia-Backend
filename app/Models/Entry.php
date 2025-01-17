<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'correlative',
        'entry_datetime',
        'code_pay',
        'quantity',
        'status_pay',
        'status_entry',
        'user_id',
        'event_id',
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
        'entry_datetime' => 'between',
        'code_pay'       => 'like',
        'quantity'       => '=',
        'status_pay'     => 'like',
        'status_entry'   => 'like',
        'user_id'        => '=',
        'event_id'       => '=',
        'person_id'      => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id'       => 'desc',
        'code_pay' => 'desc',
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
}
