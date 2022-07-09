<?php

namespace App\Services\Prescreening;

use App\Constants\Constants;
use App\Query\Master\MJenisKelamin;
use App\Query\Master\MKabupaten;
use App\Query\Master\MKecamatan;
use App\Query\Master\MKelurahan;
use App\Query\Master\MPropinsi;
use App\Query\Master\MStatusPernikahan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiData {

    public static function prescreening($params)
    {

        $request = [
            'trx_id' => '01',
            'nik' => $params['no_ktp']
        ];
        try {
            $response = Http::withHeaders([
                'token' => env('DIGI_TOKEN')
            ])->contentType("application/json")
            ->post(config('services.dukcapil.host'),$request);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            $result = $response->json();
            if(in_array($result['status'],['200'])) {
                $point = true;
                $item = $result['data'];
                Constants::MODEL_MAIN[$params['modul']]::digiData([
                    'id' => $params['id'],
                    'nama' => $item['nama_lengkap'],
                    'tempat_lahir' => $item['tempat_lahir'],
                    'id_jenis_kelamin' => MJenisKelamin::idByNama($item['jenis_kelamin']),
                    'tgl_lahir' => self::formatDate($item['tanggal_lahir']),
                    'alamat' => $item['alamat'],
                    'id_status_perkawinan' => MStatusPernikahan::idByNama($item['status_perkawinan']),
                    // 'id_propinsi' => MPropinsi::idByNama($item['propinsi']),
                    // 'id_kabupaten' => MKabupaten::idByNama($item['kabupaten']),
                    // 'id_kecamatan' => MKecamatan::idByNama($item['kecamatan']),
                    // 'id_kelurahan' => MKelurahan::idByNama($item['kelurahan']),
                ]);
                //proses mengupdate informasi nasabah
            }
            else $point = false;
            return [
                'poin' => $point,
                'message' => $result['message'], // diisi response message
                'request_body' => $request,
                'response_data' => $result
            ];
        } catch (\Throwable $th) {
            return [
                'poin' => null,
                'message' => $th->getMessage(), // diisi response message
                'request_body' => $request,
                'response_data' => $th
            ];
        }
    }

    public static function formatDate($date)
    {
        $data = explode('/',$date);
        return $data[2].'-'.$data[1].'-'.$data[0];
    }
}
