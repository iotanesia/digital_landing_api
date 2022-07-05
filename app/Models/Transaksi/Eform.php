<?php

namespace App\Models\Transaksi;

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

class Eform extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'eform';
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
        'foto',
        'is_pipeline',
        'is_cutoff',
        'is_prescreening',
        'platform',
        'nirk',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refCabang()
    {
        return $this->belongsTo(MCabang::class,'id_cabang','id_cabang');
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
}
