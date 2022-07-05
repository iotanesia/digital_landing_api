<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MMetodeEksternal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_metode_eksternal';

}
