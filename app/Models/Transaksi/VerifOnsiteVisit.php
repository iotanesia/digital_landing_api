<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifOnsiteVisit extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'verifikasi_onsite_visit';
    protected $connection = 'transaksi';
}
