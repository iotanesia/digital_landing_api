<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifValidasiData extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'verifikasi_validasi_data';
    protected $connection = 'transaksi';
}
