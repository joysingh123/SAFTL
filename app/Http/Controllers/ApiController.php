<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailValidationApi;
use App\EmailFormat;
use App\Helpers\UtilString;
use App\Contacts;
use App\CompaniesWithDomain;
use App\Helpers\UtilEmailValidation;
use Ixudra\Curl\Facades\Curl;
class ApiController extends Controller
{
    
    public function getEmailValidationApiKey(){
        $response_array = array('status'=>"fail","msg"=>"something went wrong");
        $validation_api = EmailValidationApi::where('active','yes')->where('status','enable')->orderBy('cron_count','ASC')->get();
        if($validation_api->count() > 0){
            $response_array = array('status'=>"success","api_key"=>$validation_api->first()->api_key);
        }else{
            $response_array = array('status'=>"fail","msg"=>"No, Active Api Key Found.."); 
        }
        return response()->json($response_array);
    }
    
    public function getEmailFormatByDomain(Request $request){
        $response_array = array('status'=>"fail","msg"=>"something went wrong");
        $data = $request->json()->all(); 
        if(isset($data['domain'])){
            $domain = $data['domain'];
            $email_format = EmailFormat::where("company_domain", $domain)->orderBY('format_percentage', 'DESC')->take(2)->get();
            if($email_format->count() > 0){
                $email_format_db  = $email_format->pluck('email_format');
                $email_format_db = $email_format_db->all();
                $response_array = array('status'=>"success","email_format"=>$email_format_db);
            }else{
                $emp_size_1 = ['501 to 1000','5001 to 10000','201 to 500','1001 to 5000','10000 above'];
                $emp_size_2 = ['invalid','NA','1 to 10','11 to 50','51 to 200'];
                if(isset($data['employee_size']) && !UtilString::is_empty_string($data['employee_size'])){
                    $employee_size = $data['employee_size'];
                    $email_formats = array();
                    if(in_array($employee_size, $emp_size_1)){
                        $email_formats[] = "FLASTNAME@DOMAIN";
                    }
                    if(in_array($employee_size, $emp_size_2)){
                        $email_formats[] = "FIRSTNAME@DOMAIN";
                    }
                    $response_array = array('status'=>"success","email_format"=>$email_formats);
                }else{
                   $response_array = array('status'=>"fail","msg"=>"No email format found"); 
                }
            }
        }
        return response()->json($response_array);
    }
    public function getEmailInfo(Request $request){
        $response_array = array('status'=>"fail","msg"=>"something went wrong");
        $data = $request->json()->all();
        if(isset($data['first_name']) && isset($data['last_name']) && isset($data['domain'])){
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $domain = $data['domain'];
            $contacts = Contacts::where('first_name',$first_name)->where('last_name',$last_name)->where('domain',$domain)->get();
            if($contacts->count() > 0){
                $linkedin_id = $contacts->first()->linkedin_id;
                $companies = CompaniesWithDomain::where('linkedin_id',$linkedin_id)->first();
                $contacts->first()->company_info = $companies;
                $response_array = array('status'=>"success","contact_info"=>$contacts);
            }else{
                $response_array = array('status'=>"fail","msg"=>"No Contact found"); 
            }
        }
        return response()->json($response_array);
    }
    
    public function verifyEmail(Request $request){
        $response_array = array('status'=>"fail","msg"=>"something went wrong");
        $data = $request->json()->all();
        if(isset($data['email']) && UtilString::is_email($data['email'])){
            $email = $data['email'];
            $validation_api = EmailValidationApi::where('active','yes')->where('status','enable')->orderBy('cron_count','ASC')->get();
            if($validation_api->count() > 0){
                $validation_api = $validation_api->first();
                $api_name = $validation_api->name;
                $api_url = $validation_api->api_url;
                $api_key = $validation_api->api_key;
                $email_validation_url = UtilEmailValidation::getValidationUrl($email, $api_name, $api_url, $api_key);
                $url = $email_validation_url['email_validation_url'];
                $response = Curl::to($url)->get();
                $response_array = json_decode($response, true);
                $email_validation_response = array("status"=>"","email_validation_date"=>"","msg"=>"","error_code"=>"","email"=>$email);
                if (isset($response_array['email'])) {
                    if ($response_array['smtp_check'] && $response_array['score'] >= 0.96 && !$response_array['disposable']) {
                        $email_validation_response['status'] = "valid";
                        $email_validation_response['email_validation_date'] = date("Y-m-d H:i:s");
                    } else if ($response_array['smtp_check'] && $response_array['score'] < 0.96 && !$response_array['disposable']) {
                        $email_validation_response['status'] = "catch all";
                        $email_validation_response['email_validation_date'] = date("Y-m-d H:i:s");
                    } else {
                        $email_validation_response['status'] = "invalid";
                        $email_validation_response['email_validation_date'] = date("Y-m-d H:i:s");
                    }
                }else{
                    if(isset($response_array['error']) && $response_array['error']['code'] == 104){
                        $email_validation_response['status'] = "error";
                        $email_validation_response['msg'] = "you  have reached api usage limit";
                        $email_validation_response['error_code'] = $response_array['error']['code'];
                        $email_validation_response['email_validation_date'] = date("Y-m-d H:i:s");
                    }
                    if(isset($response_array['error']) && $response_array['error']['code'] == 999){
                        $email_validation_response['status'] = "error";
                        $email_validation_response['msg'] = "timeout";
                        $email_validation_response['error_code'] = $response_array['error']['code'];
                        $email_validation_response['email_validation_date'] = date("Y-m-d H:i:s");
                    }
                }
                $response_array = array('status'=>"success","data"=>$email_validation_response);
            }else{
                $response_array = array('status'=>"fail","msg"=>"No Active api Key Found");
            }
        }
        return response()->json($response_array);
    }
}
