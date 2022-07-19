<?php

namespace App\Services\Prescreening;

use App\Query\Transaksi\PlafondDebitur;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
class SIKPPlafondDebitur {

    public static function getAccessToken()
    {
        $provider = new GenericProvider([
            'clientId'                => config('services.skip.client_id'),    // The client ID assigned to you by the provider
            'clientSecret'            => config('services.skip.client_secret'),    // The client password assigned to you by the provider
            'redirectUri'             => 'https://dev.bankdki.co.id/konven/sikp-oauth2',
            'urlAuthorize'            => 'https://dev.bankdki.co.id/konven/sikp-oauth2/authorize',
            'urlAccessToken'          => 'https://dev.bankdki.co.id/konven/sikp-oauth2/token',
            'urlResourceOwnerDetails' => 'https://dev.bankdki.co.id/konven/sikp-oauth2/resource'
        ]);

        try {
            // Try to get an access token using the resource owner password credentials grant.
            $accessToken = $provider->getAccessToken('client_credentials');
            return [
                'accessToken' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
            ];
        } catch (IdentityProviderException $e) {
            // Failed to get the access token
            throw $e;
        }

    }


    public static function prescreening($params)
    {
        $request = [
            'nik' => $params['nik']
        ];
        try {
            $auth = self::getAccessToken();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$auth['accessToken'],
            ])->contentType("application/json")
            ->get(config('services.skip.host').'/middleware/sikpkur/plafond_debitur',$request);
            Log::info(json_encode($response->json()));
            $result = $response->json();
            if($response->getStatusCode() != 200) $point = false;
            else $point = true;
            if(in_array($result['code'],['99'])) {
                $limit_aktif = $result['data'][0]['limit_aktif'] ?? null;
                $point = $limit_aktif > 0 ? false : true;
                $data = $result['data'][0];
                $data['nomor_aplikasi'] = $params['nomor_aplikasi'];
                PlafondDebitur::prescreening($data);
            }
            return [
                'poin' => $point,  // always true
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
