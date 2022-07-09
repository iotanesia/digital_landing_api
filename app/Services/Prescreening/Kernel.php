<?php

namespace App\Services\Prescreening;

use App\Constants\Constants;
use App\Models\MMetodeEksternal;
use App\Query\Prescreening;
use Illuminate\Support\Facades\Log;
class Kernel {

    public static function rules($params)
    {
        try {
            if(!$params['path']) throw new \Exception("path fungsi belum dibuat ".$params['path'], 500);
            $jenis = $params['jenis'];
            if($jenis == 'service'){
                $proses = $params['path']::prescreening($params); // fungsi service \App\Services\Prescreening\DhnBI::prescreening($param)
                if($proses['poin']) $keterangan = 'Success';
                else $keterangan = 'Failed';
                // save database
               Constants::MODEL_PRESCREENING[$params['modul']]::prescreening([
                    'metode' => 'service',
                    'keterangan' => $keterangan,
                    'id_prescreening_modul' => $params['id'],
                    'status' => $proses['poin'],
                    'id_prescreening_rules' => $params['id_prescreening_rules'],
                    'request' => json_encode($proses['request_body']),
                    'response' => json_encode( $proses['response_data']),
                ]);

                return $proses['poin'];
            }
        } catch (\Throwable $th) {
            throw $th;
        }

    }

}
