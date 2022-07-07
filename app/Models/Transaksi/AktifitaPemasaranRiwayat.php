<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AktifitaPemasaranRiwayat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'aktifitas_pemasaran_riwayat';
    protected $connection = 'transaksi';

    public $fillable = [
        "id_aktifitas_pemasaran",
        "id_tujuan_pemasaran",
        "id_cara_pemasaran",
        "informasi_aktifitas",
        "foto",
        "lokasi",
        "waktu_aktifitas",
        "tanggal_aktifitas",
        "mulai_aktifitas",
        "selesai_aktifitas",
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
    ];
}
