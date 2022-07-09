<?php

namespace App\Query\Skema;
use App\ApiHelper as Helper;
use App\Models\Skema\RulesPrescreening as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class RulesPrescreening {

    public static function getRules($request)
    {
        return Model::where(function ($query) use ($request){
            $query->where('id_metode',$request['id_metode']);
            $query->where('id_prescreening_skema',$request['id_prescreening_skema']);
        })->first();
    }

    public static function getRulesCutoff($request)
    {
        return Model::whereHas('',function ($query) use ($request){
            $query->where('is_cutoff',1);
            $query->where('id_prescreening_skema',$request['id_prescreening_skema']);
        })->get();
    }

}
