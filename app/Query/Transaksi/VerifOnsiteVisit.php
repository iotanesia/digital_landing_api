<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\VerifOnsiteVisit as Model;
use App\Models\Transaksi\Pipeline as ModelPipeline;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VerifOnsiteVisit {
    public static function byIdPiperine($id_pipeline)
    {
        $data = Model::where('id_pipeline', $id_pipeline)->first();

        return ['items' => $data];
    }

    public static function storeOnsiteVisit($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(!$request->foto) $require_fileds[] = 'foto';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $params = $request->all();
            $params['foto'] =(string) Str::uuid().'.png';
            $store = Model::create($params);

            $pipeline = ModelPipeline::find($store->id_pipeline);
            if(!$pipeline) throw new \Exception("Data not found.", 400);
            $pipeline->step_analisa_kredit = Constants::ON_SITE_VISIT;
            $pipeline->save();
            if($is_transaction) DB::commit();
            Storage::put($store['foto'], base64_decode($request->foto));
            return ['items' => $store];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
