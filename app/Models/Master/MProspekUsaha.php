<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MProspekUsaha extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'prospek_usaha';
    protected $connection = 'master';
}
