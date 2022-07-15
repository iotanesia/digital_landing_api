<?php

namespace App\Models\Transaksi;

use App\Models\Master\MKabupaten;
use App\Models\Master\MKecamatan;
use App\Models\Master\MKelurahan;
use App\Models\Master\MPropinsi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AktifitasPemesaranProfilUsaha extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'aktifitas_pemasaran_profil_usaha';
    protected $connection = 'transaksi';

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
