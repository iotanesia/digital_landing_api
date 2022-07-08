<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AktifitasPemasaranPrescreening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'aktifitas_pemasaran_prescreening';
    protected $connection = 'transaksi';

    protected $fillable = [
        'id_aktifitas_pemasaran',
        'id_prescreening_rules',
        'metode',
        'keterangan',
        'status',
        'request',
        'response',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
