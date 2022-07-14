<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisKelengkapanDokumen extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'jenis_kelengkapan_dokumen';
    protected $connection = 'master';
}
