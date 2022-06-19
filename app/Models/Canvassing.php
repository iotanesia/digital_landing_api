<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Canvassing extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'canvassing';

    public $fillable = [
        'id',
        'nik',
        'nama',
        'no_hp',
        'status',
        'email',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'id_propinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_kelurahan',
        'id_produk',
        'id_jenis_produk',
        'id_sub_produk',
        'npwp',
        'step',
        'nirk',
        'kode_cabang',
        'id_jenis_instansi',
        'id_jenis_pekerjaan',
        'nama_pasangan',
        'nomor_aplikasi',
        'id_canvassing',
        'foto',
        'platfrom',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    const STS_COLD = 'COLD';
    const STS_HOT = 'HOT';

    const STEP_PENGAJUAN_BARU = 0;
    const STEP_INPUT_CANVASSING = 1;
    const STEP_PROSES_CANVASSING = 2;
    const STEP_SUDAH_CANVASSING = 3;

    const WEB = 'WEB';
    const PUSAT = 'PUSAT';
    const MOBILE = 'MOBILE';

    public function refAktifitas()
    {
        return $this->belongsTo(Aktifitas::class,'id_canvassing','id');
    }

    public function manyAktifitas()
    {
        return $this->hasMany(Aktifitas::class,'id_canvassing','id');
    }


    public function refRm()
    {
        return $this->belongsTo(User::class,'nirk','nirk');
    }

    public function refProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id_produk');
    }
}
