<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Jobs\EformPrescreeningJobs;
use App\Models\Prescreening as ModelsPrescreening;
use Illuminate\Support\Facades\DB;
use App\Query\Eform;
class Prescreening {

    public static function process($request, $is_transaction = true){

        // if($is_transaction) DB::beginTransaction();
        try {

            $data = Eform::byId($request->id);
            if(!$data) throw new \Exception("Data not found.", 400);
            $skema = MSkemaEkternal::skema($data['items']);
            if(!$skema) throw new \Exception("Skema Ekternal belum terdaftar.", 400);
            $prescreening = (new EformPrescreeningJobs($data));
            dispatch($prescreening);



            // if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            // if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function saveAktifitas($request,$is_transaction = true)
    {
       if($is_transaction) DB::beginTransaction();
       try {
            ModelsPrescreening::create($request);
            if($is_transaction) DB::commit();
            return true;
       } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
       }
    }

}
