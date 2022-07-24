<?php

namespace App\Query\Transaksi;

use App\Constants\Constants;
use App\Models\Transaksi\Pipeline;
use Carbon\Carbon;
use App\Models\Transaksi\VerifValidasiData as Model;
use App\Query\Auth\User;
use App\Query\Transaksi\Pipeline as TransaksiPipeline;
use Illuminate\Support\Facades\DB;
class Skoring {

    public static function getDataCurrent($request)
    {
        try {
            $data = Pipeline::where(function ($query) use ($request){
                $query->where('tracking',Constants::ANALISA_KREDIT);
                $query->where('step_analisa_kredit',Constants::STEP_DATA_SEDANG_PROSES_SKORING);
                $query->where('id_user',$request->current_user->id);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){

                    if($item->id_tipe_calon_nasabah == Constants::TCN_EFORM) $data = $item->refEform;
                    elseif($item->id_tipe_calon_nasabah == Constants::TCN_AKTIFITAS_PEMASARAN) $data = $item->refAktifitasPemasaran;
                    elseif ($item->id_tipe_calon_nasabah == Constants::TCN_LEAD) $data = $item->refLeads;
                    else $data = null;

                    $id_jenis_kelamin = $data->id_jenis_kelamin ?? null;
                    return [
                        'id' => $item->id ?? null,
                        'nik' => $item->nik ?? null,
                        'nama' => $data->nama ?? null,
                        'nama_produk'=> $data->refProduk->nama ?? null,
                        'nama_sub_produk'=> $data->refSubProduk->nama ?? null,
                        'created_at' => $item->created_at ?? null,
                        'nilai' => $item->refSkorPenilaian->skor ?? 0,
                        'status' => $item->refSkorPenilaian->jenis ?? null,
                        'foto' => $id_jenis_kelamin == 2 ? 'female.png' : 'male.png'
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

    public static function approver($id)
    {
        $getInfoRM = Model::where([
            'id_pipeline' => $id
        ])->first();
        $cabangRM = $getInfoRM->id_cabang ?? null;
        $getBM = User::getInfoBM($cabangRM);
        if(!$getBM) throw new \Exception("Role BM pada cabang ".$getInfoRM->refCabang->nama_cabang.' belum diataur', 400);

        $result = new \stdClass;
        $result->nama_bm = $getBM->nama ?? null;
        $result->nirk_bm = $getBM->nirk ?? null;
        $result->jabatan_nm = $getBM->refUserRole->refRole->nama ?? null;
        return [
            'items' => $result
        ];
    }

    public static function storeAsign($request,$is_trasaction = true)
    {
        if($is_trasaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $getInfoRM = Model::where([
                'id_pipeline' => $request->id_pipeline
            ])->first();
            $cabangRM = $getInfoRM->id_cabang ?? null;
            $getBM = User::getInfoBM($cabangRM);
            if(!$getBM) throw new \Exception("Role BM pada cabang ".$getInfoRM->refCabang->nama_cabang.' belum diatur', 400);

            SkoringApproval::store([
                'id_pipeline' => $request->id_pipeline,
                'id_cabang' => $getBM->id_cabang,
                'id_user' => $getBM->id,
            ],false);

            TransaksiPipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_APPROVAL_PROSES_SKORING
            ],false);

            if($is_trasaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_trasaction) DB::beginTransaction();
            throw $th;
        }
    }

    public static function updateApprovalBm($request,$is_trasaction = true)
    {
        if($is_trasaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            TransaksiPipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => $request->status ? Constants::STEP_KELENGKAPAN_ADMINISTRASI : constants::STEP_ANALISA_SUBMIT,
                'is_revisi_scoring' => $request->status ? null : Constants::IS_ACTIVE,
                'is_rejected_by' => $request->status ? null : $request->current_user->id,
                'is_rejected_date' => $request->status ? null : Carbon::now(),
            ],false);

            if($is_trasaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_trasaction) DB::beginTransaction();
            throw $th;
        }
    }

    public static function storeReject($request,$is_trasaction = true)
    {
        if($is_trasaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            // TransaksiPipeline::updateStepAnalisaKredit([
            //     'id_pipeline' => $request->id_pipeline,
            //     'step_analisa_kredit' => $request->status ? Constants::STEP_KELENGKAPAN_ADMINISTRASI : constants::STEP_ANALISA_SUBMIT,
            //     'is_revisi_scoring' => $request->status ? null : 1
            // ],false);

            if($is_trasaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_trasaction) DB::beginTransaction();
            throw $th;
        }
    }
}
