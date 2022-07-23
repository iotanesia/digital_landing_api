<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkorDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'skor_detail';
    protected $connection = 'master';
}
