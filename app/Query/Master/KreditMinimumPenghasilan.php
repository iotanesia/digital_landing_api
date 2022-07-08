<?php

namespace App\Query\Master;
use App\ApiHelper as Helper;
use App\Models\Master\KreditMinimumPenghasilan as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class KreditMinimumPenghasilan {

    public static function isActive()
    {
        return Model::where('flag_aktif',1)->orderBy('id','desc')->first();
    }
}
