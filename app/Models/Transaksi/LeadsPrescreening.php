<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadsPrescreening extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'leads_prescreening';
    protected $connection = 'transaksi';

    protected $fillable = [
        'id_leads',
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
}
