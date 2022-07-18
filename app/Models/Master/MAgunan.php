<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MAgunan extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'agunan';
    protected $connection = 'master';
}
