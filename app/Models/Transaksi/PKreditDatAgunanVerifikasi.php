<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDatAgunanVerifikasi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'proses_kredit_data_agunan_verifikasi';
    protected $connection = 'transaksi';
    public $fillable = [
        'id_pipeline',
        'foto',
        'lokasi',
        'keterangan',
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
    ];
}
