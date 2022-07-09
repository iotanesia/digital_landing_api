<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkemaPrescreening extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'prescreening_skema';
    protected $connection = 'skema';

    public function refRules()
    {
        return $this->belongsTo(RulesPrescreening::class,'id','id_prescreening_skema');
    }

    public function manyRules()
    {
        return $this->hasMany(RulesPrescreening::class,'id_prescreening_skema','id');
    }
}
