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
            $dataNasabah = Dukcapil::prescreening($params);
            if(!$dataNasabah['response']) throw new \Exception($dataNasabah['message'], 400);
            $request = [
                "SubjectRefDate" =>date('Y-m-d'),
                "Gender" => substr($dataNasabah['jenis_kelamin'],0,1),
                "MarriageStatus" => Dukcapil::convertData('status_pernikahan',$dataNasabah['status_perkawinan']),
                "EducationalStatusCode" => "04",
                "NameAsId" => $dataNasabah['nama_lengkap'],
                "FullName" => $dataNasabah['nama_lengkap'],
                "MothersName" => "",
                "BirthDate" => Carbon::parse(str_replace('/','-',$dataNasabah['tanggal_lahir']))->format('Y-m-d'),
                "BirthPlace" => $dataNasabah['tempat_lahir'],
                "Address" => $dataNasabah['alamat'],
                "Subdistrict" => $dataNasabah['kelurahan'],
                "District" => $dataNasabah['kecamatan'],
                // "City" => (string) MKabupaten::getIdClik($request->id_kabupaten),
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
                "DueDate" => date('Y-m-d', strtotime(' + '.$data['jangka_waktu'].' month', strtotime(date('Y-m-d')))),
                "OriginalAgreementNumber" => (string)$data['jangka_waktu'],
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
            return [
                'response' => $response->json()['data'],
                'message' => '' // diisi response message
            ];
        } catch (\Throwable $th) {
            // throw $th;
            return [
                'response' => false,
                'message' => $th->getMessage() // diisi response message
            ];
        }
    }

}
