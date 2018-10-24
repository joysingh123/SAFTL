<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\EmailForValidation;
use App\Helpers\UtilDebug;
use App\Traits\ValidateEmailTraits;
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
        $limit = 250;
        $emails = EmailForValidation::where('status','not processed')->get();
        if($emails->count() > 0){
            foreach ($emails AS $email){
                $check_email =  $email->email;
                $response = $this->validateEmail($check_email);
                if($response['email_status'] != ""){
                    $email->status = $response['email_status'];
                    $email->row_data = $response['response'];
                    $email->save();
                }
            }
        }
        UtilDebug::debug("end processing");
    }
}
