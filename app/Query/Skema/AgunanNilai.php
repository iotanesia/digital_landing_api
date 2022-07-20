<?php

namespace App\Query\Skema;
use App\ApiHelper as Helper;
use App\Models\Skema\AgunanNilai as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class AgunanNilai {

    public static function byIdAgunan($id_agunan)
    {
        return Model::where('id_agunan',$id_agunan)->get()->map(function ($item)
        {
            unset(
                $item->created_by,
                $item->created_at,
                $item->updated_by,
                $item->updated_at,
                $item->deleted_at,
            );
            return $item;
        });
    }

    public static function setByIdAgunan($id_agunan,$kolom)
    {
        $key = "set by id agunan {$id_agunan}-{$kolom}";
        return Helper::storageCache($key,function () use ($id_agunan,$kolom)
        {
            $data  = Model::where([
                'id_agunan' => $id_agunan,
                'kolom' => $kolom
            ])->first();
            if(!$data) return null;
            return $data->kode.' - '.$data->nilai;
        });
    }

    public static function setByIdAgunanNilai($id_agunan,$kolom)
    {
        $key = " set nilai by id agunan {$id_agunan}-{$kolom}";
        return Helper::storageCache($key,function () use ($id_agunan,$kolom)
        {
            $data  = Model::where([
                'id_agunan' => $id_agunan,
                'kolom' => $kolom
            ])->first();
            if(!$data) return null;
            return $data->kode;
        });
    }

}
