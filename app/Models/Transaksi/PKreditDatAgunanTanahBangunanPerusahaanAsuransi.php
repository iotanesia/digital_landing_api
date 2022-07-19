<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanTanahBangunanPerusahaanAsuransi extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_tanah_bangunan_perusahaan_asuransi';
    protected $connection = 'transaksi';
    public $fillable = [
        'id_proses_kredit_data_agunan_tanah_bangunan',
        'nama_perusahaan',
        'tgl_awal',
        'tgl_akhir',
        'nilai',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

}
