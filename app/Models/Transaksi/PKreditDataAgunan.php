<?php

namespace App\Models\Transaksi;

use App\Models\Master\MAgunan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PKreditDataAgunan extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'proses_kredit_data_agunan';
    protected $connection = 'transaksi';
    public $fillable = [
        'id_pipeline',
        'id_agunan',
        'ltv',
        'taksasi',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
    ];

    public function refAgunan()
    {
        return $this->belongsTo(MAgunan::class,'id_agunan','id');
    }
}
