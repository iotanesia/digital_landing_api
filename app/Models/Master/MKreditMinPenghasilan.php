<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MKreditMinPenghasilan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_kredit_minimum_penghasilan';
    
    const IS_ACTIVE = 1;
}
