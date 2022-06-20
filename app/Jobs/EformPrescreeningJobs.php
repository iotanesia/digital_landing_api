<?php

namespace App\Jobs;

use App\Query\MSkemaEkternal;
use App\SkemaEksternal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        try {

            $data = $this->data['items'];
            $skema = MSkemaEkternal::skema($data);
            $params = [
                'nik' => $data['nik'],
                'id_eform' => $data['id'],
                'rules' => 'dhn-bi',
                'metode' => 'DHN BI',
                'id_map_rules_skema_eksternal' => ''
            ];
            dd(SkemaEksternal::rules($params));
            // SkemaEksternal::rules('dhn-bi');
            // $process = (new PrescreeningJobs());
            // dispatch($process);
            // dd($skema->manyRules);

            // foreach ($skema->manyRules as $key => $value) {
            //     # code...
            // }

            // dd($data);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
