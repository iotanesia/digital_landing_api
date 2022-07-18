<?php

namespace App\Models\Transaksi;

use App\Models\Master\MAgama;
use App\Models\Master\MCabang;
use App\Models\Master\MJenisKelamin;
use App\Models\Master\MKabupaten;
use App\Models\Master\MKecamatan;
use App\Models\Master\MKelurahan;
use App\Models\Master\MProduk;
use App\Models\Master\MPropinsi;
use App\Models\Master\MStatusPernikahan;
use App\Models\Master\MSubProduk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifValidasiData extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'verifikasi_validasi_data';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_pipeline',
        'nik',
        'nama',
        'no_hp',
        'email',
        'tempat_lahir',
        'tgl_lahir',
        'npwp',
        'alamat',
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
        'status',
        'foto_ktp',
        'foto_selfie',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'id_propinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_kelurahan',
        'kodepos',
        'id_propinsi_pasangan',
        'id_kabupaten_pasangan',
        'id_kecamatan_pasangan',
        'id_kelurahan_pasangan',
        'kodepos_pasangan',
        'foto_ktp',
        'foto_selfie',
        'id_cabang',
        'nik_pasangan',
        'no_hp_pasangan',
        'email_pasangan',
        'id_jenis_kelamin_pasangan',
        'id_agama_pasangan',
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

    public function refPropinsiPasangan()
    {
        return $this->belongsTo(MPropinsi::class,'id_propinsi_pasangan','id_propinsi');
    }

    public function refKabupatenPasangan()
    {
        return $this->belongsTo(MKabupaten::class,'id_kabupaten_pasangan','id_kabupaten');
    }

    public function refKecamatanPasangan()
    {
        return $this->belongsTo(MKecamatan::class,'id_kecamatan_pasangan','id_kecamatan');
    }

    public function refKelurahanPasangan()
    {
        return $this->belongsTo(MKelurahan::class,'id_kelurahan_pasangan','id_kelurahan');
    }

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

    public function manyProfilUsaha()
    {
        return $this->hasMany(VerifProfilUsaha::class,'id_verifikasi_validasi_data','id');

    }

    // proses kredit
    public function refPKreditDataPersonal()
    {
        return $this->belongsTo(PKreditDataPersonal::class,'id_pipeline','id_pipeline');
    }
}
