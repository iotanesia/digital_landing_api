<?php

namespace App\Query\Transaksi;

use App\ApiHelper AS Helper;
use App\Constants\Constants;
use App\Jobs\MailSender;
use App\Models\Transaksi\AktifitasPemasaranPrescreening;
use App\Models\Transaksi\EfomPrescreening;
use App\Models\Transaksi\LeadsPrescreening;
use App\Models\Transaksi\AktifitasPemasaran;
use App\Models\Transaksi\Leads;
use App\Models\Transaksi\Eform;
use App\Query\Transaksi\AktifitasPemasaran as TransaksiAktifitasPemasaran;
use App\Query\Transaksi\Eform as TransaksiEform;
use App\Query\Transaksi\Leads as TransaksiLeads;
use App\View\Transaksi\VPrescreening AS View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApprovalPrescreening {

    public static function getDataCurrent($request)
    {
        try {
            $data = View::where(function ($query) use ($request){
                $query->where('is_prescreening',2);
                if($request->nik) $query->where('nik',$request->nik);
                if($request->tipe_calon_nasabah) $query->where('tipe_calon_nasabah',$request->tipe_calon_nasabah);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){
                    return [
                        'id' => $item->id,
                        'nik' => $item->nik,
                        'nama' => $item->nama,
                        'tipe_calon_nasabah' => $item->tipe_calon_nasabah,
                        'foto_selfie' => $item->foto_selfie,
                        'status_prescreening'=> 'Lolos Bersyarat',
                        'nama_rm' => $item->refUser->nama ?? null,
                        'nirk_rm' => $item->refUser->nirk ?? null,
                        'tgl_buka_rm' => $item->refUser->tgl_buka ?? null,
                    ];
                }),
                'attributes' => [
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'from' => $data->currentPage(),
                    'per_page' => (int) $data->perPage(),
                ]
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function byId($id,$type)
    {
        try {
            if($type == 'eform') $dataNasabah = TransaksiEform::byId($id);
            if($type == 'leads') $dataNasabah = TransaksiLeads::byId($id);
            if($type == 'aktifitas_pemasaran') $dataNasabah = TransaksiAktifitasPemasaran::byIdForPiperline($id);

            return [
                'items' => [
                    'id' => $dataNasabah['items']['id'] ?? null,
                    'nomor_aplikasi' => $dataNasabah['items']['nomor_aplikasi'] ?? null,
                    'nama' => $dataNasabah['items']['nama'] ?? null,
                    'nik' => $dataNasabah['items']['nik'] ?? null,
                    'email' => $dataNasabah['items']['email'] ?? null,
                    'plafond' => $dataNasabah['items']['plafond'] ?? null,
                    'no_hp' => $dataNasabah['items']['no_hp'] ?? null,
                    'ref_id' => $dataNasabah['items']['id'] ?? null,
                    'tipe_calon_nasabah' => $type,
                    'foto_selfie' => $dataNasabah['items']['foto_selfie'] ?? null,
                    'data_nasabah' => $dataNasabah['items'],
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            //code...

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function approve($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            if($request->tipe_calon_nasabah == 'eform') {
                $update = Eform::find($request->id);

            } elseif($request->tipe_calon_nasabah == 'aktifitas_pemasaran') {
                $update = AktifitasPemasaran::find($request->id);

            } else {
                $update = Leads::find($request->id); $update = Leads::find($request->id);

            }
            $update->is_prescreening = $request->status == Constants::IS_ACTIVE ? Constants::IS_ACTIVE : Constants::CUT_OFF;
            $update->rejected_at = $request->status == Constants::IS_NOL ? Carbon::now() : null;
            $update->rejected_by = $request->status == Constants::IS_NOL ? $request->current_user->nama : null;
            $update->approved_at = $request->status == Constants::IS_ACTIVE ? Carbon::now() : null;
            $update->approved_by = $request->status == Constants::IS_ACTIVE ? $request->current_user->nama : null;
            $update->save();

            if($is_transaction) DB::commit();
            if($request->status == Constants::IS_ACTIVE) {
                $mail_data = [
                    "fullname" => $update->nama,
                    "nik" => $update->nik,
                    "nomor_aplikasi" => $update->nomor_aplikasi,
                    "reciver" =>  $update->email
                ];
                $mail_send = (new MailSender($mail_data));
                dispatch($mail_send);
            }

            return ['items' => [
                'nik' => $update->nik,
                'nomor_aplikasi' => $update->nomor_aplikasi,
            ]];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function getInfoPrescreening($request, $id, $tipe) {
        try {
            if($tipe == 'eform') $data = EfomPrescreening::where('id_eform',$id)->paginate($request->limit);
            if($tipe == 'leads') $data = LeadsPrescreening::where('id_leads',$id)->paginate($request->limit);
            if($tipe == 'aktifitas_pemasaran') $data = AktifitasPemasaranPrescreening::where('id_aktifitas_pemasaran',$id)->paginate($request->limit);
        return [
            'items' => $data->getCollection()->transform(function ($item){
                return [
                    'id' => $item->id,
                    'metode' => $item->refRules->refMetode->metode ?? null,
                    'skema' => $item->refRules->refSkema->skema ?? null,
                    'status' =>  $item->keterangan,
                    'response' => isset(json_decode($item->response,true)['keterangan']) ? json_decode($item->response,true)['keterangan'] : (isset(json_decode($item->response,true)['message']) ? json_decode($item->response,true)['message'] : null)
                ];
            }),
            'attributes' => [
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'from' => $data->currentPage(),
                'per_page' => (int) $data->perPage(),
            ]
        ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
