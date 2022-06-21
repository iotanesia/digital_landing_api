<?php

namespace App\Jobs;

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
use Illuminate\Support\Facades\Log;
class EformPrescreeningJobs implements ShouldQueue
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
        try {
            $skema = MSkemaEkternal::skema($data);
            foreach ($skema->manyRules->map(function ($item){
                $item->rules = $item->refMetode->fungsi ?? null;
                $item->metode = $item->refMetode->metode ?? null;
                return $item;
            })->toArray() as $key => $rule) {
                $params = [
                    'nik' => $data['nik'],
                    'id_eform' => $data['id'],
                    'rules' => $rule['rules'],
                    'metode' => $rule['metode'],
                    'id_map_rules_skema_eksternal' => $rule['id'],
                    'data' => $data
                ];
                // kondisi jika cut of
                $process = SkemaEksternal::rules($params);
                if($rule['is_cut_off']) {
                    if(!$process) break; // stop
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally{
            Eform::updateStsPrescreening($data['id'],Prescreening::SELESAI,false);
            // proses selesai
        }
    }
}
