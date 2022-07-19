<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlafondDebitur extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'plafond_debitur';
    protected $connection = 'transaksi';

    public $fillable = [
        'nomor_aplikasi',
        'nik',
        'skema',
        'total_limit_default',
        'total_limit',
        'limit_aktif_default',
        'limit_aktif',
        'kode_bank',
        'sisa_hari',
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
    ];
}
