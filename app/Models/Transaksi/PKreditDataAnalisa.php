<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\MSubSubProduk;

class PKreditDataAnalisa extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_analisa';
    protected $connection = 'transaksi';

    protected $fillable = [
        'id_pipeline',
        'plafond_diberikan',
        'jangka_waktu',
        'nilai_angsuran',
        'maks_plafond',
        'maks_angsuran',
        'maks_jangka_waktu',
        'id_sub_sub_produk',
        'id_jenis_skema',
        'kode_plan',
        'kode_dinas',
        'id_sektor_ekonomi',
        'idir',
        'rpc',
        'kategori_debitur',
        'kategori_portofolio',
        'jenis_kredit',
        'sifat_kredit',
        'id_jenis_penggunaan',
        'orientasi_penggunaan',
        'kategori_kredit',
        'sk_bng',
        'sk_bunga',
        'pendapatan_ditangguhkan',
        'tujuan_bank_garansi',
        'jenis_bank_garansi',
        'id_lokasi_proyek',
        'sandi_realisasi',
        'rpc_sisa_penghasilan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refSubSubProduk()
    {
        return $this->belongsTo(MSubSubProduk::class,'id_sub_sub_produk','id');
    }

}
