<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Pipeline as Model;
use App\View\Transaksi\VListPipeline as View;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Models\Transaksi\AktifitasPemasaranPrescreening;
use App\Models\Transaksi\EfomPrescreening;
use App\Models\Transaksi\LeadsPrescreening;
use App\Query\Status\StsTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Query\Transaksi\AktifitasPemasaran;
use App\Query\Transaksi\Leads;
use App\Query\Transaksi\Eform;

class Pipeline {

    public static function byId($id_pipeline)
    {
        try {
            $data = View::find($id_pipeline);
            if(!$data) throw new \Exception("Data not found.", 400);

            $tipeNasabah = $data->tipe_calon_nasabah;
            $refId =  $data->ref_id;
            $dataNasabah = [];

            if($tipeNasabah == 'Eform') {
                $dataNasabah = Eform::byId($refId);
            }

            if($tipeNasabah  == 'Leads') {
                $dataNasabah = Leads::byId($refId);
            }

            if($tipeNasabah == 'Aktifitas Pemasaran') {
                $dataNasabah = AktifitasPemasaran::byIdForPiperline($refId);
            }

            return [
                'items' => [
                    'id' => $data->id,
                    'nik' => $data->nomor_aplikasi,
                    'nama' => $data->nama,
                    'nik' => $data->nik,
                    'email' => $data->email,
                    'plafond' => $data->plafond,
                    'no_hp' => $data->no_hp,
                    'ref_id' => $data->ref_id,
                    'tipe_calon_nasabah' => $data->tipe_calon_nasabah,
                    'foto_selfie' => $data->foto_selfie,
                    'data_nasabah' => $dataNasabah['items'],
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getDataCurrent($request)
    {
        try {
            $data = View::where(function ($query) use ($request){
                $query->where('id_user',$request->current_user->id);
                // $query->where('id_user',48);
                      if($request->nik) $query->where('nik',$request->nik);
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

    public static function checkNasabah($nik) {
        try {
            $data = Model::where('nik', $nik)->first();
            $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_NOL,'is_prescreening'=>Constants::IS_NOL];
            if($data && $data->tracking != Constants::DISBURSMENT) $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_ACTIVE,'is_prescreening'=>Constants::CUT_OFF];

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getInfoPrescreening($request, $id) {
        try {
            $dataPipeline = View::where('id', $id)->first();
            $dataPipeline->ref_id = 109; //hard code sementara

            if(!$dataPipeline) throw new \Exception("Data not found.", 400);
            if($dataPipeline->tipe_calon_nasabah == 'Eform') $data = EfomPrescreening::where('id_eform',$dataPipeline->ref_id)->paginate($request->limit);
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
