<?php

namespace App\Services;

use App\Models\MKabupaten;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class ClikService {

    public static function prescreening($params)
    {
        try {
            $data = $params['data'];
            // service dukcapil
            $dataNasabah = SKIPCalonSingle::prescreening($params);
            if(!$dataNasabah['response']) throw new \Exception($dataNasabah['message'], 400);
            $dataNasabah = $dataNasabah['response'];
            // dd($dataNasabah);
            $request = [
                "SubjectRefDate" => date('Y-m-d'),
                "Gender" => $dataNasabah['jns_kelamin'] == 1 ? 'P' : 'L' ,
                "MarriageStatus" => $dataNasabah['maritas_sts'],
                "EducationalStatusCode" => "04",
                "NameAsId" => $data['nama'],
                "FullName" => $data['nama'],
                "MothersName" => "",
                "BirthDate" => $data['tanggal_lahir'],
                "BirthPlace" => $data['tempat_lahir'],
                "Address" => $data['alamat_detail'],
                "Subdistrict" => $data['nama_kelurahan'],
                "District" => $data['nama_kecamatan'],
                "City" => $data['nama_kabupaten'],
                "City" => "0198",
                "PostalCode" => $data['kode_pos'],
                "Country" => "ID",
                "IdentityType" => "1",
                "IdentityNumber" => $data['nik'],
                "NPWP" => $data['npwp'],
                "PhoneNumber" => $data['no_hp'],
                "CellphoneNumber" => $data['no_hp'],
                "EmailAddress" => $data['email'],
                "JobCode" => "008",
                "Workplace" => "Olihalus Bandung",
                "CodeOfBusiness" => "112000",
                "WorkplaceAddress" => "Mega Kuningan Jakarta",
                "ContractCategoryCode" => "F01",
                "ContractTypeCode" => "P05",
                "ContractPhase" => "RQ",
                "ContractRequestDate" => "2020-10-10",
                "Currency" => "IDR",
                "ApplicationAmount" => (string) $data['plafon'],
                "DueDate" => Carbon::now()->addMonth($data['jangka_waktu'])->format('Y-m-d'),
                "OriginalAgreementNumber" => (string) $data['jangka_waktu'],
                "OriginalAgreementDate" => date('Y-m-d'),
                "Role" => "B",
                "ProviderContractNo" => "",
                "ProviderApplicationNo" => "20101010",
                "CBContractCode" => ""
            ];
            $response = Http::contentType("application/json")
            ->post(config('services.clik.host').'/NewApplicationEnquiry',$request);
            Log::info(json_encode($response->json()));
            if($response->getStatusCode() != 200) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            $result = $response->json();
            if(!in_array($result['Code'],['E08','10-143'])) throw new \Exception(json_encode($response->json()), $response->getStatusCode());
            //E08
            //10-143
            return [
                'response' => $result,
                'message' => $result['Description'], // diisi response message
                'request_body' => $request,
                'response_data' => $result

            ];
        } catch (\Throwable $th) {
            return [
                'response' => false,
                'message' => $th->getMessage(), // diisi response message
                'request_body' => $request,
                'response_data' => $result
            ];
        }
    }

}
