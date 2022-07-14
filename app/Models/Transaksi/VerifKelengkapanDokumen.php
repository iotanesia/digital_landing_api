<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifKelengkapanDokumen extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'verifikasi_kelengkapan_dokumen';
    protected $connection = 'transaksi';
}
