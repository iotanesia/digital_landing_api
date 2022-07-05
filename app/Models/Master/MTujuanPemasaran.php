<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MTujuanPemasaran extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tujuan_pemasaran';
    protected $primaryKey = 'id_tujuan_pemasaran';
    protected $connection = 'master';
    public $fillable = [
        'nama_tujuan_pemasaran',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model){
            $model->created_by = request()->current_user->id;
        });
        static::updating(function ($model){
            $model->updated_by = request()->current_user->id;
        });
        static::deleting(function ($model){
            $model->deleted_by = request()->current_user->id;
        });
    }
}
