<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\VerifKelengkapanDokumen as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Models\Master\JenisKelengkapanDokumen;
use App\Models\Transaksi\Pipeline;
use App\Models\Transaksi\VerifValidasiData;
use App\Query\Transaksi\Pipeline as TransaksiPipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class VerifKelengkapanDokumen {

    public static function getByIdPiperine($id_pipeline)
    {

      try {
        $check = VerifValidasiData::where('id_pipeline',$id_pipeline)->first();
        if(!$check) throw new \Exception("Data belum divalidasi", 400);
        $dokumen = $check->refSubProduk->dokumen ?? null;
        $data = JenisKelengkapanDokumen::with([
            'refVerifKelengkapanDokumen' => function ($query) use ($id_pipeline){
                $query->where('id_pipeline',$id_pipeline);
            }
        ])
        ->whereIn('id',explode(';',$dokumen))
        ->orderBy('id','asc')
        ->get()->map(function ($item){
            $item->id_dokumen = $item->id;
            $item->path = $item->refVerifKelengkapanDokumen->path ?? null;
            $item->created_at = $item->refVerifKelengkapanDokumen->created_at ?? null;
            $item->created_by = $item->refVerifKelengkapanDokumen->created_by ?? null;
            $item->updated_at = $item->refVerifKelengkapanDokumen->updated_at ?? null;
            $item->updated_by = $item->refVerifKelengkapanDokumen->updated_by ?? null;
            $item->id = $item->refVerifKelengkapanDokumen->id ?? null;

            unset(
                $item->refVerifKelengkapanDokumen,
                $item->deleted_at,
                $item->deleted_by
            );
            return $item;
        });
        return ['items' => $data];
      } catch (\Throwable $th) {
        throw $th;
      }
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
                $store->id_jenis_kelengkapan_dokumen =$item['id_dokumen'];
                $store->path =$filename;
                $store->created_by = $request->current_user->id;
                $store->save();
                Storage::put($filename, base64_decode($item['file']));
            }

            TransaksiPipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_ANALISA_KELENGKAPAN
            ],false);
            if($is_transaction) DB::commit();

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
