<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanDeposito extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_deposito';
    protected $connection = 'transaksi';
    public $fillable = [
        'id_proses_data_agunan',
        'jenis_deposito',
        'nama_pemilik',
        'alamat_pemilik',
        'nomor_bilyet',
        'bank_cabang_penerbit',
        'tgl_penerbitan',
        'tgl_jatuh_tempo',
        'nilai_nominal',
        'nilai_taksasi',
        'nilai_valuta_asing',
        'nilai_tukar',
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
        'penilaian_menurut_bank',
        'bi_pengikatan_intenal',
        'bi_pengikatan_notaril',
        'bi_bukti_dok_kepemilikan',
        'bi_dati',
        'utillized_amout',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
    public function manyAset()
    {
        return $this->hasMany(PKreditDatAgunanDepositoAset::class,'id_proses_kredit_data_agunan_deposito','id');
    }
    public function manyAsuransi()
    {
        return $this->hasMany(PKreditDatAgunanDepositoPerusahaanAsuransi::class,'id_proses_kredit_data_agunan_deposito','id');
    }
}
