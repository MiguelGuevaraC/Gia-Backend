<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bitacora extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'action',
        'table_name',
        'record_id',
        'description',
        'data',
        'ip_address',
        'user_agent',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
