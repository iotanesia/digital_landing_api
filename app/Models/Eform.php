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
        'nik',
        'nama',
        'no_hp',
        'email',
        'tempat_lahir',
        'tanggal_lahir',
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
        'kode_aplikasi',
        'step',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'alamat_detail',
    ];

    const STEP_PENGAJUAN_BARU = 0;
    const STEP_INPUT_EFORM = 1;
    const STEP_PROSES_EFORM = 2;
    const STEP_SUDAH_EFORM = 3;

}
