<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SopAgunan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sop_agunan';
    protected $connection = 'master';
}
