<?php

namespace App\Services\Prescreening;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DhnBI {

    public static function prescreening($params)
    {
        $request = [
            'no_ktp' => $params['no_ktp'],
        ];
        try {
            $response = Http::contentType("application/json")
            ->post(config('services.dwh.host').'/middleware/dwh/dhn_bi',$request);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            $result = $response->json();
            if(in_array($result['status'],['00'])) $point = true;
            else $point = false;
            return [
                'poin' => $point,
                'message' => $result['keterangan'], // diisi response message
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
