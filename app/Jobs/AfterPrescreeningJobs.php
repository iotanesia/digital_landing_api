<?php

namespace App\Jobs;

use App\Constants\Constants;
use App\Query\Skema\RulesPrescreening;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AfterPrescreeningJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $status;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$status)
    {
        $this->data = $data;
        $this->status = $status;
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
        Log::info('start after prescreening '.$data['id'].'-'.$modul.'-'.Carbon::now());
        try {

            $params['id_metode'] = 6; // click
            $params['id_prescreening_modul'] = $data['id'];
            $params['id_prescreening_skema'] = $data['id_prescreening_skema'];
            $params['status'] = $this->status;
            // jika terdapat metode prescreening clik
            $rules = RulesPrescreening::getRules($params);
            if($rules){
                $params['id_prescreening_rules'] = $rules->id ?? null;
                $result = Constants::MODEL_PRESCREENING[$modul]::byRules($params);
                if($result){
                    // jika 1 lolos, 2 lolos dengan approval
                    if(in_array($result->status,[1,2])) {
                        $params['status'] = $result->status;
                        Constants::MODEL_MAIN[$modul]::isPrescreeningSuccess($params);
                    }else Constants::MODEL_MAIN[$modul]::isPrescreeningFailed($params);
                }
            }else Constants::MODEL_MAIN[$modul]::isPrescreeningSuccess($params);
        Log::info('end after prescreening '.$data['id'].'-'.$modul.'-'.Carbon::now());
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
