<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkorDetailNilai extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'skor_detail_nilai';
    protected $connection = 'master';
}
