<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MJenisPekerjaan extends Model
{
    use HasFactory;
    protected $table = 'jenis_pekerjaan';
    protected $connection = 'master';

    public $fillable = [
        'nama',
        'kode'
    ];
}
