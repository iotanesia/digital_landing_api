<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\VerifValidasiData as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;

class VerifValidasiData {

    public static function byIdPipeline($id_pipeline)
    {
        return Model::where('id_pipeline',$id_pipeline)->first();
    }

    public static function store($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $attr = $request->all();
            $data = Model::where('id_pipeline',$request->id_pipeline)->first();
            if($data) {
                $data->fill($attr);
                $data->save();
            } else $data = Model::create($attr);

            VerifProfilUsaha::store($request,$data->id,false);

            Pipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_ANALISA_VERIF_DATA
            ],false);
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }



}
