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
        Log::info('start after prescreening '.$data['id'].'-'.$modul.'-'.Carbon::now());
        try {

            $data['id_metode'] = 6; // click
            $data['id_prescreening_modul'] = $data['id'];
            // jika terdapat metode prescreening clik
            $rules = RulesPrescreening::getRules($data);
            if($rules){
                $data['id_prescreening_rules'] = $rules->id ?? null;
                $result = Constants::MODEL_PRESCREENING[$modul]::byRules($data);
                if($result){
                    // jika 1 lolos, 2 lolos dengan approval
                    if(in_array($result->status,[1,2])) {
                        $data['status'] = $result->status;
                        Constants::MODEL_MAIN[$modul]::isPrescreeningSuccess($data);
                    }else Constants::MODEL_MAIN[$modul]::isPrescreeningFailed($data);
                }
            }
        Log::info('end after prescreening '.$data['id'].'-'.$modul.'-'.Carbon::now());
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
