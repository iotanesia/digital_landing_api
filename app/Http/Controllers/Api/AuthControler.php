<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\ApiUsers;
use App\Services\Signature;
use Illuminate\Support\Facades\File;

class AuthControler extends Controller
{
    public function login(Request $request)
    {
        try {
            return Helper::resultResponse(
                ApiUsers::authenticateuser($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }


}
