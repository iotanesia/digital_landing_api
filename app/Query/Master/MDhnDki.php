<?php

namespace App\Query\Master;
use App\Models\MDhnDki as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;

class MDhnDki {

    public static function bynik($nik){
        return Model::where('d24ktp',$nik)->first();
    }

    public static function prescreening($params){
        $data = Model::where('d24ktp','ilike','%'.$params['nik'].'%')->first();
        return $data ?? null;
    }

}
