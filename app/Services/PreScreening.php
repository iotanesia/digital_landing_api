<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class PreScreening {
    static function convertData($param,$value) {
        try {
            if($param){

            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public static function dukcapil($request)
    {
        try {
            $response = Http::withHeaders([
                'token' => env('DIGI_TOKEN')
            ])->contentType("application/json")
            ->post(config('services.dukcapil.host'),[
                "trx_id" => 1,
                "nik" => $request->nik
            ]);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            $dataNasabah = $response->json()['data'];

            


            dd($response->json()['data']);
            $mappingData = ["SubjectRefDate" =>date('Y-m-d'),
                            "Gender" => substr($dataNasabah,0,1),
                            "MarriageStatus" => self::convertData('status_pernikahan', $dataNasabah),
                            "EducationalStatusCode" => "04",
                            "NameAsId" => "ANDRA NABILA F",
                            "FullName" => "ANDRA NABILA F",
                            "MothersName" => "",
                            "BirthDate" => "1991-01-02",
                            "BirthPlace" => "Bandung",
                            "Address" => "JL. MERAK NO. 2",
                            "Subdistrict" => "Kulon",
                            "District" => "Wetan",
                            "City" => "0198",
                            "PostalCode" => "11112",
                            "Country" => "ID",
                            "IdentityType" => "1",
                            "IdentityNumber" => "31207232506962428",
                            "NPWP" => "",
                            "PhoneNumber" => "",
                            "CellphoneNumber" => "088809849772",
                            "EmailAddress" => "UTRI@YMAIL.COM",
                            "JobCode" => "008",
                            "Workplace" => "Olihalus Bandung",
                            "CodeOfBusiness" => "112000",
                            "WorkplaceAddress" => "Mega Kuningan Jakarta",
                            "ContractCategoryCode" => "F01",
                            "ContractTypeCode" => "P05",
                            "ContractPhase" => "RQ",
                            "ContractRequestDate" => "2020-10-10",
                            "Currency" => "IDR",
                            "ApplicationAmount" => "10000000",
                            "DueDate" => "2023-10-10",
                            "OriginalAgreementNumber" => "24",
                            "OriginalAgreementDate" => "2020-10-29",
                            "Role" => "B",
                            "ProviderContractNo" => "",
                            "ProviderApplicationNo" => "20101010",
                            "CBContractCode" => ""];
            return $response->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function clik($nik)
    {
        try {
            $response = Http::contentType("application/json")
            ->post(config('services.clik.host'),[
                "trx_id" => 1,
                "nik" => $nik
            ]);
            dd($response->json());
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            return $response->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
