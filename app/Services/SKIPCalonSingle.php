<?php

namespace App\Services;

use App\Models\MKabupaten;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\GenericProvider;
use GuzzleHttp\Client;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Subscriber;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
class SKIPCalonSingle {

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
            ->get(config('services.skip.host').'/v1/sikpkur/konven/calon_single',$request);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            $result = $response->json();
            if(!in_array($result['code'],['12'])) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            return [
                'response' => $result['data'],
                'message' => $result['message'],
                'request_body' => $request,
                'response_data' => $result
            ];
        } catch (\Throwable $th) {
            $result = json_decode($th->getMessage(),true);
            return [
                'response' => false,
                'message' => $result['message'],
                'request_body' => $request,
                'response_data' => $result
            ];
        }
    }

}
