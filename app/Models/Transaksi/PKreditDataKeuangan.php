<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDataKeuangan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'proses_kredit_data_keuangan';
    protected $connection = 'transaksi';

    public $fillable = [
       'id_pipeline',
       'omzet_usaha',
       'hpp_usaha',
       'sewa_kontrak_usaha',
       'gaji_pegawai_usaha',
       'telp_listrik_air_usaha',
       'transportasi_usaha',
       'pengeluaran_lainnya_usaha',
       'penghasilan_lainnya_usaha',
       'belanja_rumah_tangga_umah_tangga',
       'sewa_kontrak_rumah_tangga',
       'pendidikan_rumah_tangga',
       'telp_listrik_air_rumah_tangga',
       'transportasi_rumah_tangga',
       'pengeluaran_lainnya_rumah_tangga',
       'angsuran_pinjaman_saat_ini_rumah_tangga',
       'angsuran_kredit_bank_dki_rumah_tangga',
       'idir',
       'rpc',
       'profitability',
       'rpc_sisa_penghasilan',
       'created_at',
       'updated_at',
       'deleted_at',
       'created_by',
       'updated_by'
    ];

    public function refPipeline()
    {
        return $this->belongsTo(Pipeline::class,'id_pipeline','id');
    }
}
