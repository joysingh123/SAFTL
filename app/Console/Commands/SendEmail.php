<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Mail\TestEmailViaSendgrid;
use \Illuminate\Support\Facades\Mail;
use App\Contacts;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'used for sending test email';

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
        $data = ['message' => 'This is a test!'];
        $limit = 5;
//        $contacts = Contacts::where('final','not processed')->take($limit)->get();
//        if($contacts->count() > 0){
//            foreach ($contacts AS $con){
//                $email = $con->email;
//                if(UtilString::is_email($email)){
//                    $response = Mail::to($email)->send(new TestEmailViaSendgrid($data));
//                    $con->final = "sent";
//                    $con->save();
//                }else{
//                    $con->final = "Email Not Exist";
//                    $con->save();
//                }
//            }
//        }
        $con_email = array("ajay.agrawal9022@gmail.com","sumit@salesaladin.com","tosumitgoel@gmail.com","sumitg1276.1@gmail.com","sumitg1276.2@gmail.com","sumitg1276.3@gmail.com","sumitg1276.4@gmail.com","sumitg1276.5@gmail.com","sumit-goel@hcl.com");
        foreach($con_email AS $email){
            $response = Mail::to($email)->send(new TestEmailViaSendgrid($data));
        }
        UtilDebug::debug("End processing");
    }
}
