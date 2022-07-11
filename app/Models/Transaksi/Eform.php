<?php

namespace App\Models\Transaksi;

use App\Models\Master\MAgama;
use App\Models\Master\MCabang;
use App\Models\Master\MJenisKelamin;
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
use App\Models\Status\StsPrescreening;

class Eform extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'eform';
    protected $connection = 'transaksi';
    public $fillable = [
        'id',
        'nomor_aplikasi',
        'nik',
        'cif',
        'nama',
        'no_hp',
        'email',
        'tempat_lahir',
        'tgl_lahir',
        'npwp',
        'alamat',
        'alamat_usaha',
        'id_jenis_kelamin',
        'id_agama',
        'id_status_perkawinan',
        'nama_pasangan',
        'tempat_lahir_pasangan',
        'tgl_lahir_pasangan',
        'alamat_pasangan',
        'id_produk',
        'id_sub_produk',
        'plafond',
        'jangka_waktu',
        'id_cabang',
        'is_pipeline',
        'is_cutoff',
        'is_prescreening',
        'platform',
        'long',
        'lat',
        'foto_ktp',
        'foto_selfie',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'id_client_api',
        'id_user',
        'id_propinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_kelurahan',
    ];

    public function refCabang()
    {
        return $this->belongsTo(MCabang::class,'id_cabang','id_cabang');
    }

    public function refJenisKelamin()
    {
        return $this->belongsTo(MJenisKelamin::class,'id_jenis_kelamin','id');
    }

    public function refAgama()
    {
        return $this->belongsTo(MAgama::class,'id_agama','id');
    }

    public function refProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id');
    }

    public function refSubProduk()
    {
        return $this->belongsTo(MSubProduk::class,'id_sub_produk','id');
    }

    public function refStatusPerkawinan()
    {
        return $this->belongsTo(MStatusPernikahan::class,'id_status_perkawinan','id');
    }

    public function refPipeline()
    {
        return $this->belongsTo(Pipeline::class,'nomor_aplikasi','nomor_aplikasi');
    }

    public function refStsPrescreening()
    {
        return $this->belongsTo(StsPrescreening::class,'is_prescreening','id_prescreening');
    }

    public function manyProfilUsaha()
    {
        return $this->hasMany(EformProfilUsaha::class,'id_eform','id');
    }
}
