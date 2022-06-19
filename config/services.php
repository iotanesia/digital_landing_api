<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bri' => [
        'host' => env('BRI_HOST','https://sandbox.partner.api.bri.co.id'),
        'key' => env('BRI_KEY_PRIVATE_NAME'),
    ],
    'mandiri' => [
        'host' => env('MANDIRI_HOST','https://mandiri-snap.linkaja.dev:4101'),
        'key' => env('MANDIRI_KEY_PRIVATE_NAME','mandiri'),
    ],
    'digi' => [
        'host' => env('DIGI_HOST','https://api.digidata.ai/bankdkidigi_poc'),
        'token' => env('DIGI_TOKEN','YjQ5MWMzYmMtNWQwNS00MGU2LTk1MmEtNjliOTNiMDEzYWMz'),
    ],
    'sikp' => [
        'host' => env('SIKP_HOST','https://dev.bankdki.co.id/v1/sikpkur/konven'),
    ],
    'monas' => [
        'host' => env('MONAS_HOST','http://10.32.3.149:9992/monash/api'),
    ],
    'jamkrida' => [
        'host' => env('MONAS_HOST','http://10.32.3.149:9992/monash/api'),
    ],
    'clik' => [
        'host' => env('CLIK_HOST','http://10.32.3.168:12010'),
    ],
    'dukcapil' => [
        'host' => env('CLIK_HOST','https://api.digidata.ai/bankdkidigi_poc/integrated_id_plus'),
    ],
    'skip' => [
        'host' => env('SKIP_HOST','https://dev.bankdki.co.id'),
        'client_id' => env('SKIP_CLIENT_ID'),
        'client_secret' => env('SKIP_CLIENT_SECRET'),
    ],

];
