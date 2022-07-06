<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DwhService {

    public static function mikro($params)
    {
        try {
            $response = Http::contentType("application/json")
            ->post(config('services.dwh.host').'/middleware/dwh/mikro',[
                'no_rekening' => $params['no_rekening'],
                'no_ktp' => $params['no_ktp'],
            ]);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception("Data not found.", 400);
            $result = $response->json();
            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
