<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Skema\RulesPrescreening;

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

    public function refRules()
    {
        return $this->belongsTo(RulesPrescreening::class,'id_prescreening_rules','id');
    }

    public function refKolektibilitas()
    {
        // sementara diarahkan ke nik
        return $this->belongsTo(Kolektibilitas::class,'nik','nik');

    }
}
