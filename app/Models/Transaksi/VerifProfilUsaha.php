<?php

namespace App\Models\Transaksi;

use App\Models\Master\MKecamatan;
use App\Models\Master\MKelurahan;
use App\Models\Master\MPropinsi;
use App\Models\Master\MKabupaten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifProfilUsaha extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'verifikasi_profil_usaha';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_verifikasi_validasi_data',
        'npwp',
        'nama_usaha',
        'profil_usaha',
        'alamat_usaha',
        'mulai_operasi',
        'id_perizinan',
        'lat',
        'lng',
        'id_propinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_kelurahan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refPropinsi()
    {
        return $this->belongsTo(MPropinsi::class,'id_propinsi','id_propinsi');
    }

    public function refKabupaten()
    {
        return $this->belongsTo(MKabupaten::class,'id_kabupaten','id_kabupaten');
    }

    public function refKecamatan()
    {
        return $this->belongsTo(MKecamatan::class,'id_kecamatan','id_kecamatan');
    }

    public function refKelurahan()
    {
        return $this->belongsTo(MKelurahan::class,'id_kelurahan','id_kelurahan');
    }
}
