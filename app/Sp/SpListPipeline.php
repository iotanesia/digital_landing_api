<?php

namespace App\Sp;
use App\Models\Transaksi\Leads as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Constants;

class SpListPipeline {

    public static function getDataCurrent($request)
    {
        // dd($request->current_user->id);
        $request->current_user->id = 48;
        $data = DB::connection('transaksi')
        ->select(DB::raw('select sp_list_pipeline('.$request->current_user->id.')')); // dikembangin lagi parameternya
        $page = $request->page ?? 1;
        $paginate = $request->limit ?? 15;
        $offSet = ($page * $paginate) - $paginate;
        $itemsForCurrentPage = array_slice($data, $offSet, $paginate, true);
        $data = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($data), $paginate, $page);
        return [
            'items' => $data->getCollection()->transform(function ($item){
                $pipeline = explode(',',$item->sp_list_pipeline);
                return [
                    'id' => $pipeline[0],
                    'nomor_aplikasi' => $pipeline[1],
                    'nik' => $pipeline[2],
                    'tipe_calon_nasabah' => $pipeline[3],
                    'nama' => $pipeline[4],
                    'foto_selfie' => $pipeline[5],
                    'id_parent' => $pipeline[6],
                    'created_at' => $pipeline[7],
                ];
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
