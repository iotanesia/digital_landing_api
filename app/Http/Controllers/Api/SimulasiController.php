<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\User;

class SimulasiController extends Controller
{
    public function process(Request $request)
    {
        try {

            $amount = self::pmt($request->bunga, $request->jangka_waktu, $request->plafond_kredit);
            // echo "Your payment will be &pound;" . round($amount,2) . " a month, for " . $months . " months";
            $data['estimasi_angsuran_per_bulan'] =  $amount;
            $data['estimasi_minimal_penghasilan'] = self::estimationSalary($amount);

            return Helper::resultResponse(
               ['items' => $data]
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    static function pmt($interest, $months, $loan) {
        $months = $months;
        $interest = $interest / 1200;
        $amount = $interest * -$loan * pow((1 + $interest), $months) / (1 - pow((1 + $interest), $months));
        return round($amount,2);
    }

    static function estimationSalary($amount)
    {
        return round($amount / (60/100));
    }


}
