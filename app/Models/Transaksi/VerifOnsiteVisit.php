<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Transaksi\Pipeline;

class VerifOnsiteVisit extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'verifikasi_onsite_visit';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_pipeline',
        'foto',
        'keterangan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refPipeline()
    {
        return $this->belongsTo(Pipeline::class,'id_pipeline','id');
    }
}
