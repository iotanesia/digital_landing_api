<?php

namespace App\View\Transaksi;

use App\Models\Transaksi\Kolektibilitas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VListPipeline extends Model {

    use HasFactory;
    protected $table = 'v_list_pipeline';
    protected $connection = 'transaksi';

    public function refKolektibilitas()
    {
        // sementara diarahkan ke nik
        return $this->belongsTo(Kolektibilitas::class,'nik','nik');

    }
}
