<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Pipeline as Model;
use App\View\Transaksi\VListPipeline as View;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Query\Status\StsTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Pipeline {

    public static function byId($id_pipeline)
    {
        try {
            $data = View::find($id_pipeline);
            if(!$data) throw new \Exception("Data not found.", 400);

            return [
                'items' => [
                    'id' => $data->id,
                    'nik' => $data->nomor_aplikasi,
                    'nama' => $data->nama,
                    'nik' => $data->nik,
                    'email' => $data->email,
                    'plafond' => $data->plafond,
                    'no_hp' => $data->no_hp,
                    'tipe_calon_nasabah' => $data->tipe_calon_nasabah,
                    'foto_selfie' => $data->foto_selfie,
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
                        'foto_selfie' => $item->foto_selfie
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

    // update data rm
     /*
        - notif email
    */
}
