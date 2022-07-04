<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MJenisPekerjaan extends Model
{
    use HasFactory;
    protected $table = 'jenis_pekerjaan';
    protected $primaryKey = 'jenis_pekerjaan_id';
    protected $connection = 'master';

    public $fillable = [
        'jenis_pekerjaan_id',
        'jenis_pekerjaan_id',
    ];
}
