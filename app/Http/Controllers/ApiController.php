<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailValidationApi;
use App\EmailFormat;
use App\Helpers\UtilString;
use App\Contacts;
use App\CompaniesWithDomain;
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
}
