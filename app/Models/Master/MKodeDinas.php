<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MKodeDinas extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kode_dinas';
    protected $connection = 'master';
}
