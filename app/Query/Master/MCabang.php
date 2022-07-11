<?php

namespace App\Query\Master;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Models\Master\MCabang as Model;
use Illuminate\Support\Facades\DB;

class MCabang {

    public static function byId($id)
    {
        return ['items' => Model::where('id_cabang', $id)->first()];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nama_cabang) $query->where('nama_cabang','ilike',"%$request->nama_cabang%");
                if($request->id_propinsi) $query->where('id_propinsi',$request->id_propinsi);
                $query->whereNotNull('lat');
                $query->whereNotNull('lng');
                $query->where('id_cabang_koor','!=',701);
            })->paginate($request->limit);
            return [
                'items' => $data->items(),
                'attributes' => [
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'from' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                ]
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->nama_cabang) $require_fileds[] = 'nama_cabang';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store = Model::create($request->all());
            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function updated($request,$id,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $update = Model::find($id);
            if(!$update) throw new \Exception("Data not found.", 400);
            $update->update($request->all());
            if($is_transaction) DB::commit();
            return $update;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function destroy($id,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $delete = Model::destroy($id);
            if($is_transaction) DB::commit();
            return $delete;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function getDistanceBetweenPoints($request) {
        $lat1 = $request->lat;
        $lon1 = $request->long;
        $counter = 0;
        $data = Model::select('*')
                ->whereNotNull('lng')
                ->whereNotNull('lat')
                ->get();
        foreach($data as $key => $val) {
            $lat2 = str_replace(',','.', $val->lat);
            $lon2 = str_replace(',','.', $val->lng);
            $theta = $lon1 - $lon2;
            $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
            $miles = acos($miles);
            $miles = rad2deg($miles);
            $miles = $miles * 60 * 1.1515;
            $feet  = $miles * 5280;
            $yards = $feet / 3;
            $kilometers = $miles * 1.609344;
            $meters = $kilometers * 1000;
            if($key == 0) {
                $counter = $miles;
                $MCabang = $val;

            } elseif($counter > $miles) {
                $counter = $miles;
                $MCabang = $val;
            }
        }

        return ['items' => $MCabang];
    }

    public static function getCabangBbrv($id)
    {
        return Model::where('id_cabang', $id)->first()->cabang_bbrv;
    }
}
