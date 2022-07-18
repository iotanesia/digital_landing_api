<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MIntegritasUsaha extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'integritas_usaha';
    protected $connection = 'master';
}
