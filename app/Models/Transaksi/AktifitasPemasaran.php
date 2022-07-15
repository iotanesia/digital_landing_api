<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Status\StsPrescreening;
use App\Models\Status\StsCutoff;
use App\Models\Status\StsPipeline;
use App\Models\Master\MJenisKelamin;
use App\Models\Master\MAgama;
use App\Models\Master\MStatusPernikahan;
use App\Models\Master\MProduk;
use App\Models\Master\MSubProduk;
use App\Models\Master\MCabang;
use App\Models\Status\StsAktifitasPemasaran;

class AktifitasPemasaran extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'aktifitas_pemasaran';
    protected $connection = 'transaksi';

    public $fillable = [
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
        'id_user',
        'status',
        'is_pipeline',
        'is_cutoff',
        'is_prescreening',
        'foto_ktp',
        'foto_selfie',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refStsPrescreening()
    {
        return $this->belongsTo(StsPrescreening::class,'is_prescreening','id_prescreening');
    }

    public function refStsCutoff()
    {
        return $this->belongsTo(StsCutoff::class,'is_cutoff','id_cutoff');
    }

    public function refStsPipeline()
    {
        return $this->belongsTo(StsPipeline::class,'is_pipeline','id_pipeline');
    }

    public function refJenisKelamin()
    {
        return $this->belongsTo(MJenisKelamin::class,'id_jenis_kelamin','id');
    }

    public function refAgama()
    {
        return $this->belongsTo(MAgama::class,'id_agama','id');
    }

    public function refStatusPernikahan()
    {
        return $this->belongsTo(MStatusPernikahan::class,'id_status_perkawinan','id');
    }

    public function refProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id');
    }

    public function refSubProduk()
    {
        return $this->belongsTo(MSubProduk::class,'id_sub_produk','id');
    }

    public function refCabang()
    {
        return $this->belongsTo(MCabang::class,'id_cabang','id_cabang');
    }

    public function refStatus()
    {
        return $this->belongsTo(StsAktifitasPemasaran::class,'status','id');
    }

    public function refPipeline()
    {
        return $this->belongsTo(Pipeline::class,'nomor_aplikasi','nomor_aplikasi');
    }

    public function manyProfilUsaha()
    {
        return $this->hasMany(AktifitasPemesaranProfilUsaha::class,'id_aktifitas_pemasaran','id');
    }

}
