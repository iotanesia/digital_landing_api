<?php

namespace App\Models\Transaksi;

use App\Models\Auth\User;
use App\Models\Master\MCabang;
use App\Models\Master\MKabupaten;
use App\Models\Master\MKecamatan;
use App\Models\Master\MKelurahan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\MProduk;
use App\Models\Master\MPropinsi;
use App\Models\Master\MStatusPernikahan;
use App\Models\Master\MSubProduk;

class Pipeline extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pipeline';
    protected $connection = 'transaksi';
    public $fillable = [
        'nomor_aplikasi',
        'id_tipe_calon_nasabah',
        'id_user',
        'tanggal',
        'foto_ktp',
        'status',
        'tracking',
        'tgl_lahir',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refUser()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }

    public function refEform()
    {
        return $this->belongsTo(Eform::class,'nomor_aplikasi','nomor_aplikasi');
    }
}