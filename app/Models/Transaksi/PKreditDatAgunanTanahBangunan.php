<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanTanahBangunan extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_tanah_bangunan';
    protected $connection = 'transaksi';
    public $fillable = [
        'id',
        'id_proses_data_agunan',
        'tanggal_pemeriksaan',
        'luas_tanah',
        'luas_bangunan',
        'harga_tanah',
        'harga_bangunan',
        'no_bukti_kepemilikan',
        'pemilik_agunan',
        'alamat_agunan',
        'jenis_dokumen',
        'tgl_jatuh_tempo',
        'no_sertifikat',
        'tgl_sertifikat',
        'nama_sertifikat',
        'no_sugs',
        'tgl_gs',
        'usia_bangunan',
        'lebar_jalan_depan',
        'no_imb',
        'tgl_imb',
        'atas_nama_imb',
        'alamat_imb',
        'spesifikasi_bangunan',
        'nilai_penyusutan_bangunan',
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
        'utillized_amout',
        'lokasi',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    public function manyAset()
    {
        return $this->hasMany(PKreditDatAgunanTanahBangunanAset::class,'id_proses_kredit_data_agunan_tanah_bangunan','id');
    }
    public function manyAsuransi()
    {
        return $this->hasMany(PKreditDatAgunanTanahBangunanPerusahaanAsuransi::class,'id_proses_kredit_data_agunan_tanah_bangunan','id');
    }
}
