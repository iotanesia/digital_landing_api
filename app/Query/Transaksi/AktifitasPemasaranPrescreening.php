<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\AktifitasPemasaranPrescreening as Model;
use Illuminate\Support\Facades\DB;

class AktifitasPemasaranPrescreening {

      // prescreening
      public static function prescreening($request,$is_transaction = true)
      {
          if($is_transaction) DB::beginTransaction();
          try {
              $request['id_aktifitas_pemasaran'] = $request['id_prescreening_modul'];
              Model::create($request);
              if($is_transaction) DB::commit();
          } catch (\Throwable $th) {
              if($is_transaction) DB::rollback();
              throw $th;
          }
      }
}
