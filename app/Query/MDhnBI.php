<?php

namespace App\Query;
use App\Models\MDhnBi as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;

class MDhnBI {

    public static function bynik($nik){
        return Model::where('d30ktp',$nik)->first();
    }

    public static function prescreening($params){
        $data = Model::where('d30ktp','ilike','%'.$params['nik'].'%')->first();
        return $data ?? null;
    }

}
