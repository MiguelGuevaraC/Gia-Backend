<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeAsset extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'code',
        'encrypted',
        'description',
        'barcode_path',
        'qrcode_path',
        'reservation_id',
        'lottery_ticket_id',
        'entry_id',
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

        'code' => 'like',
        //'encrypted' => 'like',
        'barcode_path' => 'like',
        'qrcode_path' => 'like',
        'reservation_id' => '=',
        'lottery_ticket_id' => '=',
        'entry_id' => '=',
        'created_at' => 'between',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
        'business_name' => 'desc',
        'names' => 'desc',

    ];

    public function environments()
    {
        return $this->hasMany(Environment::class);
    }
      public function scan_logs()
    {
        return $this->hasMany(ScanLog::class,'code_asset_id');
    }
}
