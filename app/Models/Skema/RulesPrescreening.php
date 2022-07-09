<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RulesPrescreening extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'prescreening_rules';
    protected $connection = 'skema';

    public function refMetode()
    {
        return $this->belongsTo(MetodePrescreening::class,'id_metode','id');
    }

    public function refSkema()
    {
        return $this->belongsTo(SkemaPrescreening::class,'id_skema','id');
    }

}
