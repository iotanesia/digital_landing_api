<?php

namespace App;

use App\Models\MMetodeEksternal;
use App\Query\Prescreening;
use Illuminate\Support\Facades\Log;
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
                        'request_body' => json_encode([
                            'nik' => $params['nik']
                        ]),
                        'response_data' => $proses ? 'Data ditemukan' : 'Data tidak ditemukan',
                    ]);
                    return true;
                }else {
                    Prescreening::saveAktifitas([
                        'metode' => $params['metode'],
                        'keterangan' => 'Data tidak ditemukan',
                        'id_eform' => $params['id_eform'],
                        'status' => 0,
                        'id_map_rules_skema_eksternal' => $params['id_map_rules_skema_eksternal'],
                        'request_body' => json_encode([
                            'nik' => $params['nik']
                        ]),
                        'response_data' => $proses ? 'Data ditemukan' : 'Data tidak ditemukan',
                    ]);
                    return false;
                }

            }

            if($jenis == 'service'){
                $proses = config('eksternal.'.$path.'.query')::prescreening($params);
                Log::info(config('eksternal.'.$path.'.query'));
                if($proses['response']){
                    Prescreening::saveAktifitas([
                        'metode' => $params['metode'],
                        'keterangan' => $proses['message'],
                        'id_eform' => $params['id_eform'],
                        'status' => 1,
                        'id_map_rules_skema_eksternal' => $params['id_map_rules_skema_eksternal'],
                        'request_body' => json_encode($proses['request_body']),
                        'response_data' => json_encode( $proses['response_data']),
                    ]);
                    return true;
                }else {
                    Prescreening::saveAktifitas([
                        'metode' => $params['metode'],
                        'keterangan' => $proses['message'],
                        'id_eform' => $params['id_eform'],
                        'status' => 0,
                        'id_map_rules_skema_eksternal' => $params['id_map_rules_skema_eksternal'],
                        'request_body' => json_encode($proses['request_body']),
                        'response_data' => json_encode( $proses['response_data']),
                    ]);
                    return false;
                }
            }

        }
    }

}
