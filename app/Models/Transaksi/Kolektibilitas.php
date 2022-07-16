<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kolektibilitas extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kolektibilitas';
    protected $connection = 'transaksi';
}
