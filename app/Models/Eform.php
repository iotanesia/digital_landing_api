<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Eform extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'eform';

    public $fillable = [
        'id',
        'nik',
        'nama',
        'no_hp',
        'email',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'nama_pasangan',
        'tempat_lahir_pasangan',
        'tanggal_lahir_pasangan',
        'id_propinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_kelurahan',
        'kode_pos',
        'lokasi',
        'lokasi_usaha',
        'id_jenis_produk',
        'id_produk',
        'id_sub_produk',
        'nomor_aplikasi',
        'step',
        'status',
        'id_canvassing',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'alamat_detail',
        'foto',
        'status_prescreening'
    ];

    const STEP_PENGAJUAN_BARU = 0;
    const STEP_INPUT_EFORM = 1;
    const STEP_PROSES_EFORM = 2;
    const STEP_SUDAH_EFORM = 3;
    const LOLOS = 1;
    const TIDAK_LOLOS = 0;

    public function refCabang()
    {
        return $this->belongsTo(MCabang::class,'kode_cabang','kode_cabang');
    }

    public function refProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id_produk');
    }

    public function refSubProduk()
    {
        return $this->belongsTo(MSubProduk::class,'id_sub_produk','id');
    }

    public function refPropinsi()
    {
        return $this->belongsTo(MPropinsi::class,'id_propinsi','id_propinsi');
    }

    public function refKelurahan()
    {
        return $this->belongsTo(MKelurahan::class,'id_kelurahan','id_kelurahan');
    }

    public function refKecamatan()
    {
        return $this->belongsTo(MKecamatan::class,'id_kecamatan','id_kecamatan');
    }

    public function refKabupaten()
    {
        return $this->belongsTo(MKabupaten::class,'id_kabupaten','id_kabupaten');
    }

    public function manyProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id_produk');
    }

    public function manyAktifitas()
    {
        return $this->hasMany(Prescreening::class,'id_eform','id');
    }

}
