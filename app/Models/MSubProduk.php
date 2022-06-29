<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MSubProduk extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_sub_produk';

    public $fillable = [
        'kode_sub_produk',
        'nama_sub_produk',
        'kode_produk',
        'suku_bunga',
        'rasio_pembayaran_utang',
        'maks_period',
        'maks_plafon',
        'banner_sub_produk',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public static function cekPlafon($id, $plafon) {
        try {
            $sub_produk = MSubProduk::find($id);

            return ($plafon > $sub_produk->maks_plafon || $plafon > $sub_produk->min_plafon);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

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
