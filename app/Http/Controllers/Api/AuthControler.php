<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\User;
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
            return Helper::resultResponse([
                'items' => Helper::getUserJwt($request, TRUE)
            ]);
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

}
