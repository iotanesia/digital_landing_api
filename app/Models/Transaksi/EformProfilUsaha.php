<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EformProfilUsaha extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'eform_profil_usaha';
    protected $connection = 'transaksi';
    public $fillable = [
        'npwp',
        'nama_usaha',
        'profil_usaha',
        'alamat_usaha',
        'mulai_operasi',
        'id_perizinan',
        'lat',
        'lng',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
