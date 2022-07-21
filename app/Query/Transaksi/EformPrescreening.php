<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\EformPrescreening as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;

class EformPrescreening {

    public static function sikpMetode($id_eform)
    {
        return Model::join('skema.prescreening_rules','transaksi.eform_prescreening.id_prescreening_rules','skema.prescreening_rules.id')
        ->where('id_metode',Constants::MTD_RULES_DHN_DKI)
        ->where('id_eform',$id_eform)->first();
    }

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

      public static function byRules($request)
      {
            return Model::where(function ($query) use ($request)
            {
                 $query->where('id_prescreening_rules',$request['id_prescreening_rules']);
                 $query->where('id_eform',$request['id_prescreening_modul']);
            })->first();
      }

      public static function getRulesCutoff($request)
      {
          return Model::whereHas('refRulesPrescreening',function ($query) use ($request){
              $query->where('is_cutoff',1);
              $query->where('id_eform',$request['id_prescreening_modul']);
          })->get();
      }
}
