<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MWilayahPemasran extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'wilayah_pemasaran';
    protected $connection = 'master';
}
