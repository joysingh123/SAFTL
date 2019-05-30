<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailValidationApi;
use App\EmailFormat;
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
        if($request->has('domain')){
            $domain = $request->get('domain');
            $email_format = EmailFormat::where("company_domain", $domain)->orderBY('format_percentage', 'DESC')->take(2)->get();
            if($email_format->count() > 0){
                echo $email_format;
            }
        }
    }
}
