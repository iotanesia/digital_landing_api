<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\MapPenjaminanSegmen as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class MapPenjaminanSegmen {

    public static function retrive($request)
    {
        return [
            'items' => Model::where('nirk',$request->current_user->nirk)->first()
        ];
    }
}
