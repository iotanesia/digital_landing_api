<?php

namespace App\View\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VPrescreening extends Model {
    use HasFactory;

    protected $table = 'v_list_prescreening';
    protected $connection = 'transaksi';

    public static function getDataCurrent($request) {

    }
}
