<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MJenisPekerjaan extends Model
{
    use HasFactory;
    protected $table = 'master_jenis_pekerjaan';
    protected $primaryKey = 'jenis_pekerjaan_id';

    public $fillable = [
        'jenis_pekerjaan_id',
        'jenis_pekerjaan_id',
    ];
}
