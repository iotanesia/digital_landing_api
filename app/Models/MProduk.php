<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MProduk extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_produk';

    public $fillable = [
        'id_produk',
        'nama_produk',
        'deskripsi_produk',
        'foto_produk',
        'banner_produk',
        'suku_bunga',
        'rasio_pembayaran_utang',
        'maks_period',
        'maks_plafon',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
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
