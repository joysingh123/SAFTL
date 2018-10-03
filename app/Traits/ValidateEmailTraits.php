<?php

namespace App\Traits;
use App\EmailValidationApi;
use App\Helpers\UtilConstant;
use Ixudra\Curl\Facades\Curl;
use App\Helpers\UtilEmailValidation;
use App\EmailValidation;
use App\Helpers\UtilString;

trait ValidateEmailTraits {
    
    public function validateEmail($email) {
        $validation_api = EmailValidationApi::where('status','enable')->get();
        $email_validation_status = array();
        foreach ($validation_api AS $va) {
            $api_name = $va->name;
            $api_url = $va->api_url;
            $api_key = $va->api_key;
            $email_validation_url = UtilEmailValidation::getValidationUrl($email, $api_name, $api_url, $api_key);
            $url = $email_validation_url['email_validation_url'];
            $response = Curl::to($url)->get();
            $email_validation_status = array('email_status'=>"",'verified_by'=>$api_name,'response'=>$response);
            $response_array = json_decode($response, true);
            if (isset($response_array['email']) || isset($response_array['address'])) {
                $email_status = "";
                if ($email_validation_url['verified_by'] == UtilConstant::EMAIL_VALIDATION_API_MAILBOXLAYER_NAME) {
                    $email_validation_status['verified_by'] = UtilConstant::EMAIL_VALIDATION_API_MAILBOXLAYER_NAME;
                    if ($response_array['smtp_check']) {
                        $email_validation_status['email_status'] = "valid";
                    } else if (!$response_array['smtp_check'] && $response_array['score'] > 0.48) {
                        $email_validation_status['email_status'] = "catch all";
                    } else {
                        $email_validation_status['email_status'] = "invalid";
                    }
                }

                if ($email_validation_url['verified_by'] == UtilConstant::EMAIL_VALIDATION_API_ZEROBOUNCE_NAME) {
                    $email_validation_status['verified_by'] = UtilConstant::EMAIL_VALIDATION_API_ZEROBOUNCE_NAME;
                    if ($response_array['status'] == 'valid') {
                        $email_validation_status['email_status'] = "valid";
                    } else if ($response_array['status'] == 'catch-all') {
                        $email_validation_status['email_status'] = "catch all";
                    } else {
                        $email_validation_status['email_status'] = "invalid";
                    }
                }
                if(!UtilString::is_empty_string($email_validation_status['email_status'])){
                    $email_validation = new EmailValidation();
                    $email_validation->email = $email;
                    $email_validation->status = $email_validation_status['email_status'];
                    $email_validation->verified_by = $email_validation_status['verified_by'];
                    $email_validation->raw_data = $email_validation_status['response'];
                    $email_added = $email_validation->save();
                }
                break;
            }
        }
        return $email_validation_status;
    }
}
?>
