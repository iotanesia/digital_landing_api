<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkorSkemaNilai extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'skoring_skema_nilai';
    protected $connection = 'skema';
}
