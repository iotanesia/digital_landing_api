<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MSektorEkonomi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sektor_ekonomi';
    protected $connection = 'master';
}
