<?php

namespace App\Query\Transaksi;

use App\Constants\Constants;
use App\Models\Transaksi\PKreditDatAgunanVerifikasi as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PKreditDatAgunanVerifikasi
{
    public static function byIdPipeline($id_pipeline)
    {
        return Model::where('id_pipeline',$id_pipeline)->get();
    }

    public static function storeDokumen($request, $is_transaction = true)
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
                $store->created_by = $request->current_user->id;
                $store->save();
                Storage::put($filename, base64_decode($item['file']));
            }

            Pipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_DATA_VERIFIKASI_AGUNAN
            ],false);
            if($is_transaction) DB::commit();

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
