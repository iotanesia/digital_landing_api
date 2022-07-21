<?php

namespace App\Query\Transaksi;
use App\View\Transaksi\VPrescreening as View;
use App\Constants\Constants;
use App\Models\Transaksi\AktifitasPemasaranPrescreening;
use App\Models\Transaksi\EformPrescreening;
use App\Models\Transaksi\LeadsPrescreening;
use Carbon\Carbon;

class VPrescreening {

    public static function getDataCurrent($request)
    {
        try {
            $data = View::where(function ($query) use ($request){
                $query->where('id_user',$request->current_user->id);
                // $query->where('id_user',48);
                if($request->nik) $query->where('nik',$request->nik);
                if($request->tipe_calon_nasabah) $query->where('tipe_calon_nasabah',$request->tipe_calon_nasabah);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){
                    return [
                        'id' => $item->id,
                        'nik' => $item->nik,
                        'nama' => $item->nama,
                        'tipe_calon_nasabah' => $item->tipe_calon_nasabah,
                        'foto_selfie' => $item->foto_selfie,
                        'status_prescreening'=> 'Lolos'
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

    public static function getInfoPrescreening($request, $id) {
        try {
            $dataPipeline = View::where('id', $id)->first();
            // $dataPipeline->ref_id = 109; //hard code sementara

            if(!$dataPipeline) throw new \Exception("Data not found.", 400);
            if($dataPipeline->tipe_calon_nasabah == 'Eform') $data = EformPrescreening::where('id_eform',$dataPipeline->ref_id)->paginate($request->limit);
            if($dataPipeline->tipe_calon_nasabah == 'Leads') $data = LeadsPrescreening::where('id_leads',$dataPipeline->ref_id)->paginate($request->limit);
            if($dataPipeline->tipe_calon_nasabah == 'Aktifitas Pemasaran') $data = AktifitasPemasaranPrescreening::where('id_aktifitas_pemasaran',$dataPipeline->ref_id)->paginate($request->limit);
        return [
            'items' => $data->getCollection()->transform(function ($item){
                return [
                    'id' => $item->id,
                    'metode' => $item->refRules->refMetode->metode ?? null,
                    'skema' => $item->refRules->refSkema->skema ?? null,
                    'status' =>  $item->keterangan,
                    'response' => isset(json_decode($item->response,true)['keterangan']) ? json_decode($item->response,true)['keterangan'] : (isset(json_decode($item->response,true)['message']) ? json_decode($item->response,true)['message'] : null)
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
}
