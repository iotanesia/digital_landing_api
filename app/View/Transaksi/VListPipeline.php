<?php

namespace App\View\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VListPipeline extends Model {
    use HasFactory;

    protected $table = 'v_list_pipeline';
    protected $connection = 'transaksi';

    public static function getDataCurrent($request) {

    }
}
