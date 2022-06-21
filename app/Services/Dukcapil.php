<?php

namespace App\Services;

use App\Models\MKabupaten;
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

        try {
            $response = Http::withHeaders([
                'token' => env('DIGI_TOKEN')
            ])->contentType("application/json")
            ->post(config('services.dukcapil.host'),[
                "trx_id" => 1,
                "nik" => $params['nik']
            ]);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            return [
                'response' => $response->json()['data'],
                'message' => '' // diisi response message
            ];

        } catch (\Throwable $th) {
            // throw $th;
            return [
                'response' => false,
                'message' => $th->getMessage() // diisi response message
            ];
        }
    }
}
