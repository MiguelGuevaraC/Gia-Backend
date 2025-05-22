<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'status',
        'password',
        'person_id',
        'rol_id',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const filters = [
        'username'             => 'like',
        'name'                 => 'like',
        'person.name'          => 'like',
        'person.business_name' => 'like',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [

        'username' => 'desc',
        'id'       => 'desc',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function isFlagData()
    {
        foreach (['phone', 'email', 'number_document',
            'names', 'father_surname', 'mother_surname'] as $campo) {
            if (empty($this->person->{$campo})) {
                return 0;
            }
        }
        return 1;
    }

    public function textFlagData()
    {
        $campos = [
            'phone'           => 'teléfono',
            'email'           => 'correo electrónico',
            'number_document' => 'documento',
            'names'           => 'nombres',
            'father_surname'  => 'apellido paterno',
            'mother_surname'  => 'apellido materno',
        ];

        $faltantes = [];

        foreach ($campos as $campo => $descripcion) {
            if (empty($this->person->{$campo})) {
                $faltantes[] = $descripcion;
            }
        }

        return empty($faltantes)
        ? 'Datos Completos'
        : 'Faltan los siguientes datos: ' . implode(', ', $faltantes);
    }

}
