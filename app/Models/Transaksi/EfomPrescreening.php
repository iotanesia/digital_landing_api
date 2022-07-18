<?php

namespace App\Models\Transaksi;

use App\Models\Skema\RulesPrescreening;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfomPrescreening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'eform_prescreening';
    protected $connection = 'transaksi';

    protected $fillable = [
        'id_eform',
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


    public function refParent()
    {
        return $this->belongsTo(Eform::class,'id_eform','id');
    }

    public function refRulesPrescreening()
    {
        return $this->belongsTo(RulesPrescreening::class,'id','id_prescreening_rules');
    }

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
