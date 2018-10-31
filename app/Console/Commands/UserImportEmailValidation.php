<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\EmailForValidation;
use App\Helpers\UtilDebug;
use App\Traits\ValidateEmailTraits;
use App\AvailableEmail;
class UserImportEmailValidation extends Command
{
    use ValidateEmailTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:useremail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        UtilDebug::debug("start processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $limit = 300;
        $emails = EmailForValidation::where('status','not processed')->take($limit)->get();
        if($emails->count() > 0){
            $plucked_email = $emails->pluck('id');
            $plucked_email_array = $plucked_email->all();
            $result = EmailForValidation::whereIn('id', $plucked_email_array)->update(['status' => 'cron1']);
            if($result > 0){
                foreach ($emails AS $email){
                    $check_email =  $email->email;
//                    $available_email = AvailableEmail::where("email",$check_email)->get();
//                    if($available_email->count() > 0){
//                        $email->status = 'valid';
//                        $email->row_data = 'From Available Email';
//                        $email->save();
//                    }else{
//                        $response = $this->validateEmail($check_email);
//                        if($response['email_status'] != ""){
//                            $email->status = $response['email_status'];
//                            $email->row_data = $response['response'];
//                            $email->save();
//                        }
//                    }
                    $response = $this->validateEmail($check_email);
                    if($response['email_status'] != ""){
                        $email->status = $response['email_status'];
                        $email->row_data = $response['response'];
                        $email->save();
                    }
                }
            }
        }
        UtilDebug::debug("end processing");
    }
}
