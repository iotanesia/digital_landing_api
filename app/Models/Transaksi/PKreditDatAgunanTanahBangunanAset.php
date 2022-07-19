<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanTanahBangunanAset extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_tanah_bangunan_aset';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_proses_kredit_data_agunan_tanah_bangunan',
        'foto',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

}
