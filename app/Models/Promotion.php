<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'precio',
        'date_start',
        'date_end',
        'stock',
        'route',
        'status',
        'product_id',
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
        'name'        => 'like',
        'description' => 'like',

        'precio'      => '=',
        'date_start'  => 'date',
        'date_end'    => 'date',
        'stock'       => '=',
        'status'      => '=',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
