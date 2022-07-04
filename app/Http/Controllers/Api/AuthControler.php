<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Auth\User;
use App\Services\Signature;
use Illuminate\Support\Facades\File;

class AuthControler extends Controller
{
    public function login(Request $request)
    {
        try {
            return Helper::resultResponse(
                User::authenticateuser($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $user = Helper::getUserJwt($request, TRUE);
            return Helper::resultResponse([
                'items' =>  ["access_token" => Helper::createJwt($user)]
            ]);
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    public function detailUser(Request $request)
    {
        try {
            $user = Helper::getUserJwt($request);
            return Helper::resultResponse([
                'items' =>  ["access_token" => Helper::createJwt($user)]
            ]);
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

}
