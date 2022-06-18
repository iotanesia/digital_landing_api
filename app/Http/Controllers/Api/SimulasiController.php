<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\User;
use stdClass;

class SimulasiController extends Controller
{
    public function process(Request $request)
    {
        try {
            $data['estimasi_angsuran_per_bulan'] = 6000000;
            $data['estimasi_minimal_penghasilan'] = 10000000;

            return Helper::resultResponse(
               ['items' => $data]
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
}
