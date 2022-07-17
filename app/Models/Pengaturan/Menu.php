<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'menu';
    protected $connection = 'pengaturan';
}
