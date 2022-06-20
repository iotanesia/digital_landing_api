<?php

namespace App;

use App\Models\MMetodeEksternal;
use App\Query\Prescreening;

class SkemaEksternal {

    public static function rules($params)
    {
        // dd($params);
        $data = MMetodeEksternal::where('fungsi',$params['rules'])->first();
        if($data){
            $path = $data->fungsi;
            $jenis = $data->jenis;
            if($jenis == 'database'){
                $proses = config('eksternal.'.$path.'.query')::prescreening($params);
                if($proses){
                    Prescreening::saveAktifitas([
                        'metode' => $params['metode'],
                        'keterangan' => 'success',
                        'id_eform' => $params['id_eform'],
                        'status' => 1,
                        'id_map_rules_skema_eksternal' => $params['id_map_rules_skema_eksternal'],
                    ]);
                }

            }
            // dd('eksternal.'.$data->fungsi);
            // dd(config('eksternal.'.$path));
        }
    }

}
