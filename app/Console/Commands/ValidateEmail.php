<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use App\Emails;
use App\EmailValidation;
use App\BounceEmail;
use App\EmailValidationApi;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
use App\Helpers\UtilEmailValidation;

class ValidateEmail extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate Created Email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        UtilDebug::debug("start processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $response = array();
        $validation_api = EmailValidationApi::where('status','enable')->get();
        $emails = Emails::where('status', 'success')->take(10)->get();
        if($validation_api->count() > 0){
            if($emails->count() > 0){
                foreach($emails AS $email_record){
                    $email = trim($email_record->email);
                    $exist_in_email_validation = EmailValidation::where('email','=',$email)->count();
                    $exist_in_bounce_email = BounceEmail::where('email','=',$email)->count();
                    if($exist_in_email_validation <= 0 && $exist_in_bounce_email <= 0){
                        foreach($validation_api AS $va){
                            $api_name = $va->name;
                            $api_url = $va->api_url;
                            $api_key = $va->api_key;
                            $email_validation_url = UtilEmailValidation::getValidationUrl($email,$api_name,$api_url,$api_key);
                            $url = $email_validation_url['email_validation_url'];
                            $response = Curl::to($url)->get();
                            $response_array = json_decode($response,true);
                            if(isset($response_array['email']) || isset($response_array['address'])){
                                $email_status = "";
                                if($email_validation_url['verified_by'] == UtilConstant::EMAIL_VALIDATION_API_MAILBOXLAYER_NAME){
                                    if($response_array['smtp_check']){
                                        $email_status = "valid";
                                    }else if(!$response_array['smtp_check'] && $response_array['score'] > 0.48){
                                        $email_status = "catch all";
                                    }else{
                                        $email_status = "invalid";
                                    }
                                }
                                
                                if($email_validation_url['verified_by'] == UtilConstant::EMAIL_VALIDATION_API_ZEROBOUNCE_NAME){
                                    if($response_array['status'] == 'valid'){
                                        $email_status = "valid";
                                    }else if($response_array['status'] == 'catch-all'){
                                        $email_status = "catch all";
                                    }else{
                                        $email_status = "invalid";
                                    }
                                }
                                
                                if(!UtilString::is_empty_string($email_status)){
                                    $exist_in_email_validation = EmailValidation::where('email','=',$email)->count();
                                    if($exist_in_email_validation <= 0){
                                        $email_validation = new EmailValidation();
                                        $email_validation->email = $email;
                                        $email_validation->status = $email_status;
                                        $email_validation->verified_by = $email_validation_url['verified_by'];
                                        $email_validation->raw_data = $response;
                                        $email_added = $email_validation->save();
                                        if($email_added){
                                            $email_record->status = $email_status;
                                            $email_record->save();
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }else{
                $response['status']="fail";
                $response['status']="No, email found for validation";
            }
        }else{
            $response['status']="fail";
            $response['status']="No, api enabled for email validation";
        }
        UtilDebug::debug("end processing");
    }
}
