<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MSubProduk extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sub_produk';
    protected $connection = 'master';

    public $fillable = [
        'id_produk',
        'nama',
        'rasio_pembayaran_hutang',
        'maks_period',
        'maks_plafond',
        'min_plafond',
        'fitur_kredit',
        'min_period',
        'min_suku_bunga',
        'max_suku_bunga',
        'dokumen',
        'title',
        'banner',
        'deskripsi',
        'foto',
        'syarat',
        'ketentuan',
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

    public function refProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id');
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
