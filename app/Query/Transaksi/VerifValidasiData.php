<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\VerifValidasiData as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            if($request->foto_ktp && $request->foto_ktp != '') $attr['foto_ktp'] = (string) Str::uuid().'.png';
            if($request->foto_selfie && $request->foto_selfie != '') $attr['foto_selfie'] = (string) Str::uuid().'.png';
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
            if($request->foto_ktp && $request->foto_ktp != '') Storage::put($attr['foto_ktp'], base64_decode($request->foto_ktp));
            if($request->foto_selfie && $request->foto_selfie != '') Storage::put($attr['foto_selfie'], base64_decode($request->foto_selfie));
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }



}
