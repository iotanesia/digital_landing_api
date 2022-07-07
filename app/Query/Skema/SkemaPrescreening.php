<?php

namespace App\Query\Skema;
use App\ApiHelper as Helper;
use App\Models\Skema\SkemaPrescreening as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class SkemaPrescreening {

    public static function skema($request)
    {
        return Model::with(['manyRules' => function ($query){
            $query->orderBy('urutan','asc');
        }])->where(function ($query) use ($request){
            $query->where('id_produk',$request['id_produk']);
            $query->where('id_sub_produk',$request['id_sub_produk']);
        })->first();
    }

}
