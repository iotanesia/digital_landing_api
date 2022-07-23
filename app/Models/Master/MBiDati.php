<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBiDati extends Model
{
    use HasFactory;
    protected $table = 'bi_dati';
    protected $connection = 'master';

    public $fillable = [
        'id_dati',
        'nama'
    ];
}
