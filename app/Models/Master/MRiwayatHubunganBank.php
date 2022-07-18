<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MRiwayatHubunganBank extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'riwayat_hubungan_bank';
    protected $connection = 'master';
}
