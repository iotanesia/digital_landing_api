<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MapPenjaminanSegmen extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'map_pinjaman_segmen';
}
