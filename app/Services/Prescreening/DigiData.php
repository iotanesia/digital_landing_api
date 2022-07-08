<?php

namespace App\Services\Prescreening;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiData {

    public static function prescreening($params)
    {
        $request = [
            'trx_id' => 1,
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
}
