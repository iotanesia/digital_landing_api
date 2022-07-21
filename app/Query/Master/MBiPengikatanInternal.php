<?php

namespace App\Query\Master;

use App\Models\Master\MBiPengikatanInternal as Model;
use App\Constants\Constants;

class MBiPengikatanInternal
{
    public static function byId($id)
    {
        return ['items' => Model::find($id)];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){
                    return $item;
                }),
                'attributes' => [
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'from' => $data->currentPage(),
                    'per_page' => (int) $data->perPage(),
                ]
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
