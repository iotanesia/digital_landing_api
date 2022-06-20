<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MSkemaEksternal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_skema_eksternal';

    public function manyRules()
    {
        return $this->hasMany(MapRulesSkemaEksternal::class,'id_skema_eksternal','id');
    }
}
