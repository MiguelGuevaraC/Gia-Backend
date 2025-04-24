<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'stock_restante',
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

    public function recalculateStockPromotion()
    {
        $stockUsado = DB::table('detail_reservations as dr')
            ->join('reservations as r', 'dr.reservation_id', '=', 'r.id')
            ->where('dr.promotion_id', $this->id)
            ->whereNotIn('r.status', ['Caducada','Anulada'])
            ->whereNull('dr.deleted_at')
            ->sum('dr.cant');
    
        $stockInicial = $this->stock;
    
        $nuevoStock = max($stockInicial - $stockUsado, 0); // evita negativos
        $this->update(['stock_restante' => $nuevoStock]);
    }
    
}
