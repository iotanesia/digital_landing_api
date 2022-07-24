<?php

namespace App\Query\Master;
use App\ApiHelper as Helper;
use App\Models\Master\MKabupaten as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;
class MKabupaten {

    public static function idByNama($nama)
    {
        $data = Model::where('nama',$nama)->first();
        return $data->id_kabupaten ?? null;
    }

    public static function byId($id)
    {
        return ['items' => Model::where('id_kabupaten', $id)->get()->transform(function ($item){
            return [
                'id' => $item->id,
                'id_propinsi' => $item->id_propinsi ?? null,
                'id_kabupaten' => $item->id_kabupaten ?? null,
                'nama_kabupaten' => $item->nama ?? null,
                'nama_propinsi' => $item->refPropinsi->nama ?? null,
                'created_at' => $item->created_at ?? null,
                'created_by' => $item->created_by ?? null,
                'updated_at' => $item->updated_at ?? null,
                'updated_by' => $item->updated_by ?? null,
                'deleted_at' => $item->deleted_at ?? null
            ];
        })];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                if($request->id_propinsi) $query->where('id_propinsi',$request->id_propinsi);
            })
            ->orderBy('id_kabupaten','asc')
            ->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'id_propinsi' => $item->id_propinsi ?? null,
                            'id_kabupaten' => $item->id_kabupaten ?? null,
                            'nama_kabupaten' => $item->nama ?? null,
                            'nama_propinsi' => $item->refPropinsi->nama ?? null,
                            'created_at' => $item->created_at ?? null,
                            'created_by' => $item->created_by ?? null,
                            'updated_at' => $item->updated_at ?? null,
                            'updated_by' => $item->updated_by ?? null,
                            'deleted_at' => $item->deleted_at ?? null
                        ];
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

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_propinsi) $require_fileds[] = 'id_propinsi';
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
}
