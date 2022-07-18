<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDataPersonal extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_personal';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_pipeline',
        'nik_kontak_darurat',
        'nama_kontak_darurat',
        'no_hp_kontak_darurat',
        'tempat_lahir_kontak_darurat',
        'tangal_lahir_kontak_darurat',
        'alamat_kontak_darurat',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
