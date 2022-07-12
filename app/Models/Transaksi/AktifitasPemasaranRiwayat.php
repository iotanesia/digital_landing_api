<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\MTujuanPemasaran;
use App\Models\Master\MCaraPemasaran;

class AktifitasPemasaranRiwayat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'aktifitas_pemasaran_riwayat';
    protected $connection = 'transaksi';

    public $fillable = [
        "id_aktifitas_pemasaran",
        "id_tujuan_pemasaran",
        "id_cara_pemasaran",
        "informasi_aktifitas",
        "foto_selfie",
        "lokasi",
        "waktu_aktifitas",
        "tanggal_aktifitas",
        "mulai_aktifitas",
        "selesai_aktifitas",
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
    ];

    public function refMstTujuanPemasaran()
    {
        return $this->belongsTo(MTujuanPemasaran::class,'id_tujuan_pemasaran','id');
    }

    public function refMstCaraPemasaran()
    {
        return $this->belongsTo(MCaraPemasaran::class,'id_cara_pemasaran','id');
    }

    public function refMstAktifitasPemasaran()
    {
        return $this->belongsTo(AktifitasPemasaran::class,'id_aktifitas_pemasaran','id');
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
