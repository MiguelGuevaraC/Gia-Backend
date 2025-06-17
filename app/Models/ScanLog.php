<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScanLog extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'code',
        'code_asset_id',
        'ip',
        'status',
        'description',
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
        'code_asset_id' => '=',
        'ip' => '=',
        'status' => '=',
        'description' => 'like',
    ];


    public function codeAsset()
    {
        return $this->belongsTo(CodeAsset::class);
    }
}
