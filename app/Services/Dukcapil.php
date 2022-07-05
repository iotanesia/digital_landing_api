<?php

namespace App\Services;

use App\Models\Master\MKabupaten;
use App\Query\LogPrescreening;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Dukcapil {

    static function convertData($param,$value) {
        try {
            if($param == 'status_pernikahan') {
                return $value == 'BELUM KAWIN' ? "1" : "2";
            }
            if($param == 'kota') {
                // return MKabupaten::where('id_kabupaen', $value)->first()->id_clik;
                return MKabupaten::whereNotNull('id_clik', $value)->first()->id_click;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function prescreening($params)
    {
        $result = null;
        $request = [
            'trx_id' => 1,
            'nik' => $params['nik']
        ];
        try {
            $response = Http::withHeaders([
                'token' => env('DIGI_TOKEN')
            ])->contentType("application/json")
            ->post(config('services.dukcapil.host'),$request);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            $result = $response->json();
            return [
                'response' => $response->json()['data'],
                'message' => '', // diisi response message,
                'request_body' => $request,
                'response_data' => $result
            ];

        } catch (\Throwable $th) {
            $result = json_decode($th->getMessage());
            return [
                'response' => false,
                'message' => $th->getMessage(), // diisi response message
                'request_body' => $request,
                'response_data' => $result
            ];
        }
    }
}
