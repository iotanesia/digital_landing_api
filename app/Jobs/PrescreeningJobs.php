<?php

namespace App\Jobs;

use App\Constants\Constants;
use App\Query\Eform;
use App\Query\MSkemaEkternal;
use App\SkemaEksternal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Query\Prescreening;
use App\Query\Skema\RulesPrescreening;
use App\Query\Skema\SkemaPrescreening;
use App\Services\Prescreening\Kernel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class PrescreeningJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data['items'];
        $modul = $this->data['modul'];
        try {

            $skema = SkemaPrescreening::skema($data);
            if(!$skema) throw new \Exception("Skema Belum dibuat", 400);
            $data['id_prescreening_skema'] = $skema->id;
            if(isset($skema->manyRules)){
                Log::info('start prescreening '.$data['id'].'-'.$modul.'-'.Carbon::now());
                $total_rules = count($skema->manyRules) - 1;
                foreach ($skema->manyRules->map(function ($item){
                    $item->fungsi = $item->refMetode->fungsi ?? null;
                    $item->jenis = $item->refMetode->jenis ?? null;
                    $item->path = $item->refMetode->path ?? null;
                    return $item;
                })->toArray() as $key => $rule) {
                    $params = [
                        'nik' => $data['nik'],
                        'no_ktp' => $data['nik'],
                        'nomor_aplikasi' => $data['nomor_aplikasi'],
                        'id' => $data['id'],
                        'id_prescreening_modul' => $data['id'],
                        'fungsi' => $rule['fungsi'],
                        'path' => $rule['path'],
                        'jenis' => $rule['jenis'],
                        'modul' => $modul, // eform, aktifitas_pemasaran, leads
                        'id_prescreening_rules' => $rule['id'],
                    ];

                    $process = Kernel::rules($params);
                    // kondisi jika cut of
                    if(in_array($process,[null,0]) && $rule['is_cutoff']) {
                        Constants::MODEL_MAIN[$modul]::isPrescreeningFailed($params);
                        break; // stop
                    }

                    if($key == $total_rules) {
                        $pipeline = (new AfterPrescreeningJobs($this->data,$process));
                        dispatch($pipeline);
                    }
                }


            }
        } catch (\Throwable $th) {
            throw $th;
        } finally{
            // Eform::updateStepPrescreening($data['id'],Prescreening::SELESAI,false);
            // proses selesai
        }
    }
}
