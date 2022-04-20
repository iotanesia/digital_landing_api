<?php

namespace App\Patners;

use Illuminate\Support\Facades\Http;
use App\Constants\Snap;
use Illuminate\Support\Facades\Log;
class Bri {

    const host = 'https://sandbox.partner.api.bri.co.id';
    public static function getAccessToken($param)
    {
        try {
            $response = Http::timeout(5)
            ->withHeaders([
                'X-CLIENT-KEY' => $param['id_key'],
                'X-SIGNATURE' => $param['signature'],
                'X-TIMESTAMP' => $param['timestamp']
            ])
            ->contentType("application/json")
            ->post(self::host.'/snap/v1.0/access-token/b2b',[
                'grantType' => 'client_credentials'
            ]);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());
            return $response->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function access($param)
    {
        try {
            $response = Http::timeout(5)
            ->withHeaders([
                'AccessToken' => $param['AccessToken']
            ])
            ->contentType("application/json")
            ->post(self::host.'/snap/v1.0/access-token/b2b',[
                'grantType' => 'client_credentials'
            ]);
            if($response->getStatusCode() != 200) throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());
            return $response->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function accountInquiryInternal($param)
    {
        try {
            $response = Http::timeout(5)
            ->withHeaders([
                'Authorization' => 'Bearer '.$param['auth'],
                'X-SIGNATURE' => $param['signature'],
                'X-TIMESTAMP' => $param['timestamp'],
                'X-PARTNER-ID' => $param['partnerId'],
                'X-EXTERNAL-ID' => $param['externalId'],
                'CHANNEL-ID' => $param['channelId']
            ])
            ->contentType("application/json")
            ->post(self::host.$param['url'], $param['body']);
            Log::info(json_encode($response->json()));
            // dd($response);
            // if($response->getStatusCode() != 200) throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());
            return $response->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
