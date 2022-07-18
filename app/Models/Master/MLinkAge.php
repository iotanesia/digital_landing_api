<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MLinkAge extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'linkage';
    protected $connection = 'master';
}
