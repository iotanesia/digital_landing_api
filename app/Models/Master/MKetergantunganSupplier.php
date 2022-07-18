<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MKetergantunganSupplier extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'ketergantungan_supplier';
    protected $connection = 'master';
}
