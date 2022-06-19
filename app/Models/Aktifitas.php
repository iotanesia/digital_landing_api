<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktifitas extends Model
{
    use HasFactory;
    protected $table = 'canvassing_aktifitas';
    public $fillable = [
        'id_tujuan_pemasaran',
        'id_cara_pemasaran',
        'id_canvassing',
        'informasi_aktifitas',
        'foto',
        'lokasi',
        'waktu',
        'tanggal',
        'nama_rm',
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

    public function refTujuanPemasaran()
    {
        return $this->belongsTo(MTujuanPemasaran::class,'id_tujuan_pemasaran','id_tujuan_pemasaran');
    }

    public function refCaraPemasaran()
    {
        return $this->belongsTo(MCaraPemasaran::class,'id_cara_pemasaran','id_cara_pemasaran');
    }
}
