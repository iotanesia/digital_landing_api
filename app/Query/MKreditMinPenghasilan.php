<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\MKreditMinPenghasilan as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;
class MKreditMinPenghasilan {

    public static function isActive()
    {
        return Model::where('flag_aktif',Model::IS_ACTIVE)->orderBy('id','desc')->first();
    }

}
