<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDataUsaha extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_usaha';
    protected $connection = 'transaksi';
    public $fillable = [
        'id_pipeline',
        'id_riwayat_hubungan_bank',
        'id_prospek_usaha',
        'id_ketergantungan_pelanggan',
        'id_jenis_produk',
        'id_ketergantungan_supplier',
        'id_wilayah_pemasaran',
        'id_integritas_usaha',
        'lama_usaha',
        'jangka_waktu',
        'tanggal_usaha',
        'izin_usaha',
        'modal_usaha',
        'jumlah_pekerja',
        'jumlah_kredit',
        'is_link_age',
        'id_link_age',
        'is_subsidies',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];
}
