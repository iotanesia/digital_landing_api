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

}
