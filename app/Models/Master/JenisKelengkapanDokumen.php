<?php

namespace App\Models\Master;

use App\Models\Transaksi\VerifKelengkapanDokumen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisKelengkapanDokumen extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'jenis_kelengkapan_dokumen';
    protected $connection = 'master';
    public $fillable = [
        'nama',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refVerifKelengkapanDokumen()
    {
        return $this->belongsTo(VerifKelengkapanDokumen::class,'id','id_jenis_kelengkapan_dokumen');
    }
}
