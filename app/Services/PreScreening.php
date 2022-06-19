<?php

namespace App\Services;

use App\Models\MKabupaten;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class PreScreening {
    static function convertData($param,$value) {
        try {
            if($param == 'status_pernikahan') {
                return $value == 'BELUM KAWIN' ? "1" : "2";
            }
            if($param == 'kota') {
                // return MKabupaten::where('id_kabupaen', $value)->first()->id_clik;
                return MKabupaten::whereNotNull('id_clik', $value)->first()->id_click;
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
            $mappingData = ["SubjectRefDate" =>date('Y-m-d'),
                            "Gender" => substr($dataNasabah['jenis_kelamin'],0,1),
                            "MarriageStatus" => self::convertData('status_pernikahan',$dataNasabah['status_perkawinan']),
                            "EducationalStatusCode" => "04",
                            "NameAsId" => $dataNasabah['nama_lengkap'],
                            "FullName" => $dataNasabah['nama_lengkap'],
                            "MothersName" => "",
                            "BirthDate" => date('Y-m-d', strToTime($dataNasabah['tanggal_lahir'])),
                            "BirthPlace" => $dataNasabah['tempat_lahir'],
                            "Address" => $dataNasabah['alamat'],
                            "Subdistrict" => $dataNasabah['kelurahan'],
                            "District" => $dataNasabah['kecamatan'],
                            "City" => (string) MKabupaten::getIdClik($request->id_kabupaten),
                            // "City" => "0198",
                            "PostalCode" => $request->kode_pos,
                            "Country" => "ID",
                            "IdentityType" => "1",
                            "IdentityNumber" => $request->nik,
                            "NPWP" => $request->npwp,
                            "PhoneNumber" => $request->no_hp,
                            "CellphoneNumber" => $request->no_hp,
                            "EmailAddress" => $request->email,
                            "JobCode" => "008",
                            "Workplace" => "Olihalus Bandung",
                            "CodeOfBusiness" => "112000",
                            "WorkplaceAddress" => "Mega Kuningan Jakarta",
                            "ContractCategoryCode" => "F01",
                            "ContractTypeCode" => "P05",
                            "ContractPhase" => "RQ",
                            "ContractRequestDate" => "2020-10-10",
                            "Currency" => "IDR",
                            "ApplicationAmount" => (string)$request->plafon,
                            "DueDate" => date('Y-m-d', strtotime(' + '.$request->jangka_waktu.' month', strtotime(date('Y-m-d')))),
                            "OriginalAgreementNumber" => (string)$request->jangka_waktu,
                            "OriginalAgreementDate" => date('Y-m-d'),
                            "Role" => "B",
                            "ProviderContractNo" => "",
                            "ProviderApplicationNo" => "20101010",
                            "CBContractCode" => ""];

            return $mappingData;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function clik($dataSend)
    {
        try {
            $response = Http::contentType("application/json")
            ->post(config('services.clik.host').'/NewApplicationEnquiry',$dataSend);
            dd($dataSend,$response->json());
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            return $response->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
