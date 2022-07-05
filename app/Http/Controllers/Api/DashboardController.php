<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\MapPenjaminanSegmen;

class DashboardController extends Controller
{
    public function segmenPenjaminan(Request $request)
    {
        try {
            return Helper::resultResponse(
                'under maintenance'
                // MapPenjaminanSegmen::retrive($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
}
