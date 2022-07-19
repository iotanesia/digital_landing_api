<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanKios extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_kios';
    protected $connection = 'transaksi';
    public $fillable = [
        'id_proses_data_agunan',
        'tanggal_pemeriksaan',
        'nomor_dokumen',
        'nama_pemegang_hak',
        'masa_berlaku_kios',
        'luas_kios',
        'nama_pasar',
        'lokasi_kios',
        'lokasi_jaminan',
        'nilai_market',
        'collateral_class',
        'jenis_agunan',
        'sifat_agunan',
        'penerbiit_agunan',
        'cash_non_cash',
        'jenis_pengikatan',
        'coverage_obligation',
        'collateral_mortage_priority',
        'allow_acct_attached_to_coll',
        'customer_or_bank_has_coll',
        'nama_perusahaan_appraisal',
        'collateral_status',
        'coll_status_of_acct',
        'coll_utilized_amout',
        'jenis_asuransi',
        'jenis_agunan_ppap',
        'bi_penilaian_menurut_bank',
        'bi_pengikatan_intenal',
        'bi_pengikatan_notaril',
        'bi_bukti_dok_kepemilikan',
        'bi_dati',
        'utillized_amout'
    ];
    public function manyAset()
    {
        return $this->hasMany(PKreditDatAgunanKiosAset::class,'id_proses_kredit_data_agunan_kios','id');
    }
    public function manyAsuransi()
    {
        return $this->hasMany(PKreditDatAgunanKiosPerusahaanAsuransi::class,'id_proses_kredit_data_agunan_kios','id');
    }
}
