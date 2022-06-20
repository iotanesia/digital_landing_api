<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\MSkemaEksternal as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class MSkemaEkternal {

    public function skema($request)
    {
        return Model::where(function ($query) use ($request){
            // $query->where('id_tipe_produk',$request->id_tipe_produk);
            $query->where('id_jenis_produk',$request->id_jenis_produk);
            $query->where('id_produk',$request->id_produk);
        })->first();
    }

}
