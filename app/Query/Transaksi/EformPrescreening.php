<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\EfomPrescreening as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\DB;

class EformPrescreening {

      // prescreening
      public static function prescreening($request,$is_transaction = true)
      {
          if($is_transaction) DB::beginTransaction();
          try {
              $request['id_eform'] = $request['id_prescreening_modul'];
              Model::create($request);
              if($is_transaction) DB::commit();
          } catch (\Throwable $th) {
              if($is_transaction) DB::rollback();
              throw $th;
          }
      }
}
