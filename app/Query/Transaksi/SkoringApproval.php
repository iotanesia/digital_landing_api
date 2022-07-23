<?php

namespace App\Query\Transaksi;

use App\Constants\Constants;
use App\Models\Transaksi\SkoringApproval as Model;
use Illuminate\Support\Facades\DB;
class SkoringApproval
{
    public static function store($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $store =  Model::where([
                'id_pipeline' => $request['id_pipeline'],
            ])->first();
            if(!$store) $store = new Model;
            $store->fill($request);
            $store->save();

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function getDataCurrent($request) {
        $data = Model::where('id_user', $request->current_user->id)
        ->where( 'step_analisa_kredit', Constants::STEP_APPROVAL_PROSES_SKORING)
        ->paginate($request->limit);
        return [
            'items' => $data->getCollection()->transform(function ($item){
                $item->nama = $item->refPipeline->refVerifValidasiData->nama ?? null;
                $item->nama_cabang = $item->refPipeline->refVerifValidasiData->refCabang->nama_cabang ?? null;
                $item->nama_sub_produk = $item->refPipeline->refVerifValidasiData->refSubProduk->nama ?? null;
                $item->skor = $item->refPipeline->refSkoringPenilaian->skor ?? null;
                $item->jenis_skor = $item->refPipeline->refSkoringPenilaian->jenis ?? null;
                $item->nama_rm = $item->refUser->nama ?? null;
                $item->nirk_rm = $item->refUser->nirk ?? null;
                $item->cabang_rm = $item->refUser->refCabang->nama_cabang ?? null;
                $item->tgl_buka_rm = $item->refUser->tgl_buka ?? null;
                unset($item->refPipeline,$item->refUser);
                return $item;
            }),
            'attributes' => [
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'from' => $data->currentPage(),
                'per_page' => (int) $data->perPage(),
            ]
        ];
    }
}
