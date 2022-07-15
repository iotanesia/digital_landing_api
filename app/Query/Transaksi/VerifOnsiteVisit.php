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

    public static function getByIdPiperine($id_pipeline)
    {
        $data = Model::where('id_pipeline', $id_pipeline)->get();
        return ['items' => $data];
    }

    public static function storeOnsiteVisit($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(!$request->upload) $require_fileds[] = 'upload';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            if($request->delete) Model::whereIn('id',$request->delete)->delete();

            foreach ($request->upload as $key => $item) {
                $check = Model::where('id',$item['id'])->first();
                $filename = (string) Str::uuid().'.png';
                if($check) $store = $check;
                else {
                    $store = new Model;
                    $store->id_pipeline = $request->id_pipeline;
                };
                $store->foto =$filename;
                $store->keterangan =$item['keterangan'];
                $store->created_by = $request->current_user->id;
                $store->save();

                Storage::put($filename, base64_decode($item['foto']));
            }

            Pipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_ANALISA_ONSITE_VISIT
            ],false);

            if($is_transaction) DB::commit();

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
