<?php

namespace App\Query;
use App\Models\Canvassing as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;
use Carbon\Carbon;

class Canvassing {

    public static function getDataPusat($request)
    {
        try {
            $data = Model::where(function ($query) use ($request){
                $query->where('step',Model::STEP_PENGAJUAN_BARU);
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                if($request->nik) $query->where('nik',$request->nik);
            })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'nama' => $item->nama,
                            'nik' => $item->nik,
                            'created_at' => $item->created_at,
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

    public static function byId($request)
    {
        // dummy-data
        try {
            return [
                'items' => [
                    'id' => 1,
                    'nik' => '123455667789',
                    'nama_lengkap' => 'Rexy',
                    'no_hp' => '089201023911',
                    'alamat' => 'Ds KKN Penari',
                    'status' => null,
                    'produk' => 'MIKRO',
                    'kode_produk' => 'MKR'
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // eform web
    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->nama) $require_fileds[] = 'nama';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->id_propinsi) $require_fileds[] = 'id_propinsi';
            if(!$request->id_kabupaten) $require_fileds[] = 'id_kabupaten';
            if(!$request->id_kecamatan) $require_fileds[] = 'id_kecamatan';
            if(!$request->id_kelurahan) $require_fileds[] = 'id_kelurahan';
            if(!$request->kode_pos) $require_fileds[] = 'kode_pos';
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->id_produk) $require_fileds[] = 'id_produk';
            if(!$request->id_sub_produk) $require_fileds[] = 'id_sub_produk';
            if(!$request->lokasi) $require_fileds[] = 'lokasi';
            if(!$request->kode_cabang) $require_fileds[] = 'kode_cabang';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),500);
            $store = Model::create($request->all());
            $store->refAktifitas()->create(self::setParamsRefAktifitas($request,$store));
            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function setParamsRefAktifitas($request,$data)
    {
         return [
            'id_canvassing' => $data,
            'waktu' => Carbon::now()->format('H:i'),
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'nama_rm' => $data->refRm->nama ?? null,
            'lokasi' => $request->lokasi,
            'informasi_aktifitas' => 'e-form: Pengajuan Baru Via Web'
         ];
    }
}
