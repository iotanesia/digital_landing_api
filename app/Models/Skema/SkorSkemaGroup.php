<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkorSkemaGroup extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'skoring_skema_grup';
    protected $connection = 'skema';

    public function manySkemaNilai()
    {
        return $this->belongsTo(SkorSkemaNilai::class,'id_skoring_skema_grup','id');
    }
}
